const pool = require('../db/connection');
const queriesVenta = require('../utils/queriesVenta');
const queriesDetalleVenta = require('../utils/queriesDetalleVenta');
const queriesUsuario = require('../utils/queriesUsuario');
const queriesPersona = require('../utils/queriesPersona');

// Registrar una venta con transacción
exports.registrarVenta = async (req, res) => {
    const { idcliente, idusuario, tipo_comprobante, serie_comprobante, num_comprobante, detalles } = req.body;

    try {
        await pool.getConnection(async (conn) => {
            try {
                // Iniciar transacción
                await conn.beginTransaction();

                // Calcular subtotal, impuesto y total
                const subtotal = detalles.reduce((sum, item) => sum + item.precio * item.cantidad, 0);
                const impuesto = subtotal * 0.16;
                const total = subtotal + impuesto;

                // Insertar la venta
                const [ventaResult] = await conn.query(queriesVenta.insertarVenta, [
                    idcliente, idusuario, tipo_comprobante, serie_comprobante, num_comprobante, impuesto, total
                ]);

                const idventa = ventaResult.insertId;

                // Insertar los detalles de la venta
                for (const detalle of detalles) {
                    await conn.query(queriesDetalleVenta.insertarDetalleVenta, [
                        idventa, detalle.idarticulo, detalle.cantidad, detalle.precio, detalle.descuento
                    ]);
                }

                // Confirmar transacción
                await conn.commit();

                res.status(201).json({ message: 'Venta registrada con éxito', idventa });
            } catch (error) {
                await conn.rollback();
                console.error('Error en transacción:', error);
                res.status(500).json({ message: 'Error al registrar la venta' });
            } finally {
                conn.release();
            }
        });
    } catch (error) {
        console.error('Error al conectar a la base de datos:', error);
        res.status(500).json({ message: 'Error del servidor' });
    }
};

// Obtener ventas por día
exports.obtenerVentasPorDia = async (req, res) => {
    const { fecha } = req.query;

    try {
        const [result] = await pool.query(queriesVenta.ventasPorDia, [fecha]);
        res.status(200).json(result);
    } catch (error) {
        console.error('Error al obtener ventas por día:', error);
        res.status(500).json({ message: 'Error del servidor' });
    }
};

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
