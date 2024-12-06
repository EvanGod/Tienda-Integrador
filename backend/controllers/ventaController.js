const pool = require('../db/connection');
const queriesVenta = require('../utils/queriesVenta');
const queriesDetalleVenta = require('../utils/queriesDetalleVenta');
const queriesArticulo = require('../utils/queriesArticulo');
const queriesUsuario = require('../utils/queriesUsuario');
const queriesPersona = require('../utils/queriesPersona');
const pdf = require('pdfkit');

exports.registrarVenta = async (req, res) => {
    const { idusuario, idcliente, tipo_comprobante, serie_comprobante, num_comprobante, detalles } = req.body;

    let conn;
    try {
        conn = await pool.getConnection(); // Obtén la conexión

        // Inicia la transacción
        await conn.beginTransaction();

        /// 1. Validar los detalles de la venta
        for (const detalle of detalles) {
            if (detalle.cantidad <= 0) {
                throw new Error(`La cantidad para el artículo ${detalle.idarticulo} no puede ser negativa o cero.`);
            }
            if (!detalle.precio || detalle.precio <= 0) {
                throw new Error(`El precio para el artículo ${detalle.idarticulo} debe ser mayor que cero.`);
            }
            if (detalle.descuento < 0) {
                throw new Error(`El descuento para el artículo ${detalle.idarticulo} no puede ser negativo.`);
            }

            // Validar que el descuento no sea mayor al total del producto
            const totalProducto = detalle.precio * detalle.cantidad;
            if (detalle.descuento > totalProducto) {
                throw new Error(
                    `El descuento para el artículo ${detalle.idarticulo} no puede ser mayor que el total (${totalProducto}) del producto.`
                );
            }
        }

        // 2. Validar que el número de comprobante no esté repetido
        const [comprobanteExistente] = await conn.query(queriesVenta.obtenerComprobanteRepetido, [num_comprobante]);
        if (comprobanteExistente.length > 0) {
            throw new Error(`El número de comprobante ${num_comprobante} ya está registrado en la base de datos.`);
        }

        // 3. Verificar que el cliente exista
        const [cliente] = await conn.query(queriesVenta.obtenerCliente, [idcliente]);
        if (!cliente || cliente.length === 0) {
            throw new Error(`El cliente con ID ${idcliente} no existe.`);
        }

        // Calcular subtotal, impuesto y total
        const total = detalles.reduce((sum, item) => sum + (item.precio * item.cantidad) - item.descuento, 0);
        const impuesto = total * 0.16; // 16% de impuesto
        const subtotal = total - impuesto;

        // Insertar la venta
        const [ventaResult] = await conn.query(queriesVenta.insertarVenta, [
            idcliente, idusuario, tipo_comprobante, serie_comprobante, num_comprobante, impuesto, total,
        ]);

        const idventa = ventaResult.insertId;

        // 4. Insertar detalles de la venta
        for (const detalle of detalles) {
            const [articulo] = await conn.query(queriesArticulo.obtenerStock, [detalle.idarticulo]);

            // Verificar que el artículo exista y tenga suficiente stock
            if (!articulo || articulo[0].stock < detalle.cantidad) {
                throw new Error(`No hay suficiente stock para el artículo ${detalle.idarticulo}. Disponible: ${articulo[0].stock}.`);
            }

            // Actualizar el stock del artículo
            await conn.query(queriesArticulo.actualizarStock, [detalle.cantidad, detalle.idarticulo, detalle.cantidad]);

            // Insertar el detalle de la venta
            await conn.query(queriesDetalleVenta.insertarDetalleVenta, [
                idventa, detalle.idarticulo, detalle.cantidad, detalle.precio, detalle.descuento,
            ]);

            // Cambiar el estado del artículo si no tiene stock
            await conn.query(queriesArticulo.cambiarEstadoSiSinStock, [detalle.idarticulo]);
        }

        // 5. Confirmar la transacción
        await conn.commit();

        // Responder con éxito
        res.status(201).json({ message: 'Venta registrada con éxito', idventa });
    } catch (error) {
        if (conn) await conn.rollback(); // Rollback si hay un error
        console.error('Error en transacción:', error.message);
        res.status(500).json({ message: 'Error al registrar la venta', error: error.message });
    } finally {
        if (conn) conn.release(); // Liberar la conexión
    }
};


exports.obtenerVentasPorDia = async (req, res) => {
    try {
        // Ejecutar la consulta sin parámetros, ya que no se necesita fecha
        const [result] = await pool.query(queriesVenta.ventasPorDia);
        res.status(200).json(result);
    } catch (error) {
        console.error('Error al obtener ventas por día:', error);
        res.status(500).json({ message: 'Error del servidor' });
    }
};


exports.obtenerTodasLasVentas = async (req, res) => {
    try {
        // Ejecutar la consulta sin parámetros, ya que no se necesita fecha
        const [result] = await pool.query(queriesVenta.obtenerTodasLasVentas);
        res.status(200).json(result);
    } catch (error) {
        console.error('Error al obtener todas las ventas:', error);
        res.status(500).json({ message: 'Error del servidor' });
    }
}
// Obtener ventas por producto
exports.obtenerVentasPorProducto = async (req, res) => {
    try {
        const [result] = await pool.query(queriesVenta.ventasPorProducto);
        res.status(200).json(result);
    } catch (error) {
        console.error('Error al obtener ventas por producto:', error);
        res.status(500).json({ message: 'Error del servidor' });
    }
};

// Obtener ID de usuario
exports.obtenerIdUsuario = async (req, res) => {
    const { email } = req.query;

    try {
        const [result] = await pool.query(queriesUsuario.obtenerIdUsuario, [email]);
        if (result.length > 0) {
            res.status(200).json({ idusuario: result[0].idusuario });
        } else {
            res.status(404).json({ message: 'Usuario no encontrado' });
        }
    } catch (error) {
        console.error('Error al obtener ID de usuario:', error);
        res.status(500).json({ message: 'Error del servidor' });
    }
};

// Obtener ID de persona (Comprador)
exports.obtenerIdComprador = async (req, res) => {
    const { nombre } = req.query;

    try {
        const [result] = await pool.query(queriesPersona.obtenerIdComprador, [nombre]);
        if (result.length > 0) {
            res.status(200).json({ idpersona: result[0].idpersona });
        } else {
            res.status(404).json({ message: 'Comprador no encontrado' });
        }
    } catch (error) {
        console.error('Error al obtener ID de comprador:', error);
        res.status(500).json({ message: 'Error del servidor' });
    }
};

exports.obtenerDetalleVenta = async (req, res) => {
    const { idventa } = req.params;

    try {
        // Verificar que se envíe un idventa válido
        if (!idventa) {
            return res.status(400).json({ message: 'El ID de la venta es obligatorio' });
        }

        // Ejecutar la consulta con el ID de la venta
        const [result] = await pool.query(queriesDetalleVenta.obtenerDetalleVentaPorId, [idventa]);

        // Verificar si se encontraron resultados
        if (result.length === 0) {
            return res.status(404).json({ message: `No se encontraron detalles para la venta con ID ${idventa}` });
        }

        // Responder con los detalles de la venta
        res.status(200).json(result);
    } catch (error) {
        console.error('Error al obtener detalle de la venta:', error);
        res.status(500).json({ message: 'Error al obtener el detalle de la venta', error: error.message });
    }
};

exports.obtenerVentaPorId = async (req, res) => {
    const { idventa } = req.params;

    try {
        // Validar que el idventa esté presente
        if (!idventa) {
            return res.status(400).json({ message: 'El ID de la venta es obligatorio' });
        }

        // Consultar la venta por ID
        const [result] = await pool.query(queriesVenta.obtenerVentaPorId, [idventa]);

        // Verificar si se encontró la venta
        if (result.length === 0) {
            return res.status(404).json({ message: `No se encontró la venta con ID ${idventa}` });
        }

        // Responder con los detalles de la venta
        res.status(200).json(result[0]);
    } catch (error) {
        console.error('Error al obtener la venta por ID:', error);
        res.status(500).json({ message: 'Error al obtener la venta', error: error.message });
    }
};
exports.generarTicket = async (req, res) => {
    const { idventa } = req.params;

    try {
        // Obtener los datos de la venta
        const [venta] = await pool.query(queriesVenta.obtenerVentaPorId, [idventa]);
        if (!venta || venta.length === 0) {
            return res.status(404).json({ message: 'Venta no encontrada' });
        }

        // Obtener los detalles de la venta
        const [detallesVenta] = await pool.query(queriesDetalleVenta.obtenerDetallesPorIdVenta, [idventa]);
        if (!detallesVenta || detallesVenta.length === 0) {
            return res.status(404).json({ message: 'Detalles de venta no encontrados' });
        }

        // Crear el documento PDF
        const doc = new pdf({ size: 'LETTER', margin: 30 });
        doc.pipe(res); // Enviar el PDF directamente como respuesta

        // Título y encabezado del ticket
        doc.fontSize(18).text('Ticket de Venta', { align: 'center' }).moveDown(1);
        doc.fontSize(12).text(`Venta #: ${venta[0].idventa}`, { align: 'center' }).moveDown(0.5);
        doc.fontSize(12).text(`Fecha: ${venta[0].fecha_hora}`, { align: 'center' }).moveDown(0.5);
        doc.fontSize(12).text(`Comprobante: ${venta[0].tipo_comprobante} ${venta[0].num_comprobante}`, { align: 'center' }).moveDown(1);

        // Datos del cliente
        doc.text(`Cliente: ${venta[0].cliente}`, { align: 'left' }).moveDown(0.5);
        doc.text(`Vendido por: ${venta[0].usuario}`, { align: 'left' }).moveDown(1);

        // Tabla para los detalles de la venta
        doc.fontSize(12).text('Detalles de la venta:', { align: 'left' }).moveDown(0.5);

        // Cabecera de la tabla
        const columnWidths = [150, 100, 100, 100, 100]; // Ancho de cada columna
        doc.fontSize(10)
            .text('Producto', 50, doc.y)
            .text('Cantidad', 200, doc.y)
            .text('Precio', 300, doc.y)
            .text('Descuento', 400, doc.y)
            .text('Subtotal', 500, doc.y);
        doc.moveDown(0.5);

        // Dibujar línea de separación
        doc.lineJoin('round').lineWidth(0.5).moveTo(50, doc.y).lineTo(550, doc.y).stroke();
        doc.moveDown(0.5);

        // Detalles de cada producto
        let subtotal = 0;
        detallesVenta.forEach(detalle => {
            const precio = parseFloat(detalle.precio);
            const descuento = parseFloat(detalle.descuento);
            const cantidad = parseInt(detalle.cantidad);
            const productoSubtotal = (cantidad * precio) - descuento;
            subtotal += productoSubtotal;

            // Mostrar los detalles en las columnas respectivas
            doc.text(detalle.nombre, 50, doc.y);
            doc.text(cantidad.toString(), 200, doc.y);
            doc.text(`$${precio.toFixed(2)}`, 300, doc.y);
            doc.text(`$${descuento.toFixed(2)}`, 400, doc.y);
            doc.text(`$${productoSubtotal.toFixed(2)}`, 500, doc.y);
            doc.moveDown(0.5);
        });

        // Línea de separación
        doc.lineJoin('round').lineWidth(0.5).moveTo(50, doc.y).lineTo(550, doc.y).stroke();
        doc.moveDown(1);

        // Subtotal, impuestos y total
        const impuestos = parseFloat(venta[0].impuesto);
        const total = parseFloat(venta[0].total);
        const calculoSubtotal = total - impuestos;

        doc.text(`Subtotal: $${calculoSubtotal.toFixed(2)}`, { align: 'right' }).moveDown(0.5);
        doc.text(`Impuestos: $${impuestos.toFixed(2)}`, { align: 'right' }).moveDown(0.5);
        doc.fontSize(14).text(`Total: $${total.toFixed(2)}`, { align: 'right' }).moveDown(1);

        // Agradecimientos
        doc.fontSize(12).text('¡Gracias por su compra!', { align: 'center' }).moveDown(0.5);
        doc.text('Esperamos volver a verle pronto. Si tiene alguna pregunta, no dude en contactarnos.', { align: 'center' });

        // Finalizar el PDF
        doc.end();
    } catch (error) {
        console.error('Error al generar el ticket:', error);
        res.status(500).json({ message: 'Error al generar el ticket', error: error.message });
    }
};
