const db = require('../db/connection'); // Asegúrate de importar la conexión de base de datos

// Función para crear un ingreso (compra) con transacción
const crearIngreso = async (req, res) => {
  const { idproveedor, productos } = req.body; // El proveedor y los productos que se están comprando

  if (!idproveedor || !Array.isArray(productos) || productos.length === 0) {
    return res.status(400).json({ message: 'Debe proporcionar un proveedor y productos válidos' });
  }

  // Validar que las cantidades y precios de los productos sean positivos
  for (let producto of productos) {
    if (producto.cantidad <= 0 || producto.precio <= 0) {
      return res.status(400).json({ message: 'Las cantidades y precios deben ser mayores a cero' });
    }
  }

  const totalCompra = productos.reduce((total, producto) => total + (producto.cantidad * producto.precio), 0);
  const impuesto = totalCompra * 0.18; // Asumimos un impuesto del 18%
  const total = totalCompra + impuesto;

  const usuarioId = req.user.idusuario; // Obtener ID del usuario autenticado

  // Iniciar transacción
  const connection = await db.getConnection();
  await connection.beginTransaction();

  try {
    // Validar existencia del proveedor
    const [proveedorExistente] = await connection.query(`
      SELECT 1 FROM persona WHERE idpersona = ? AND tipo_persona = 'Proveedor' LIMIT 1
    `, [idproveedor]);

    if (proveedorExistente.length === 0) {
      return res.status(404).json({ message: 'Proveedor no encontrado' });
    }

    // Insertar el ingreso (compra) en la tabla ingreso
    const queryIngreso = `
      INSERT INTO ingreso (idproveedor, idusuario, tipo_comprobante, serie_comprobante, num_comprobante, fecha, impuesto, total, estado)
      VALUES (?, ?, 'Factura', ?, ?, NOW(), ?, ?, 'Activo');
    `;
    
    const valuesIngreso = [idproveedor, usuarioId, '001', '0001', impuesto, total];
    const [ingresoResult] = await connection.query(queryIngreso, valuesIngreso);
    const idIngreso = ingresoResult.insertId; // ID de la compra recién creada

    // Insertar los productos en la tabla detalle_ingreso y actualizar el stock
    const queryDetalleIngreso = `
      INSERT INTO detalle_ingreso (idingreso, idarticulo, cantidad, precio)
      VALUES (?, ?, ?, ?);
    `;
    
    for (let producto of productos) {
      // Verificar si el producto existe en la base de datos
      const [productoExistente] = await connection.query(`
        SELECT idarticulo, stock, estado FROM articulo WHERE idarticulo = ? LIMIT 1
      `, [producto.idarticulo]);

      if (productoExistente.length === 0) {
        await connection.rollback();
        return res.status(404).json({ message: `Producto con ID ${producto.idarticulo} no encontrado` });
      }

      // Si el artículo estaba en estado 0 (descontinuado) y ahora tiene stock, cambiarlo a estado 1 (activo)
      if (productoExistente[0].estado === 0) {
        await connection.query(`
          UPDATE articulo
          SET estado = 1
          WHERE idarticulo = ?
        `, [producto.idarticulo]);
      }

      // Validar que haya suficiente stock para la compra
      if (productoExistente[0].stock < producto.cantidad) {
        await connection.rollback();
        return res.status(400).json({ message: `No hay suficiente stock para el producto ${producto.idarticulo}` });
      }

      // Aplicar descuento y registrar el detalle de la compra
      const precioVenta = producto.precio;
      const descuento = precioVenta * 0.20; // 20% de descuento

      await connection.query(queryDetalleIngreso, [idIngreso, producto.idarticulo, producto.cantidad, precioVenta - descuento]);

      // Actualizar el stock del producto en la tabla articulo
      await connection.query(`
        UPDATE articulo
        SET stock = stock + ?
        WHERE idarticulo = ?
      `, [producto.cantidad, producto.idarticulo]);
    }

    // Si todo es correcto, confirmamos la transacción
    await connection.commit();
    res.status(201).json({ message: 'Ingreso creado con éxito' });

  } catch (error) {
    // Si ocurre un error, revertimos la transacción
    await connection.rollback();
    console.error('Error en la transacción:', error);
    res.status(500).json({ message: 'Error al crear el ingreso' });
  } finally {
    // Cerramos la conexión
    connection.release();
  }
};

// Función para obtener los ingresos por día
const obtenerIngresosPorDia = async (req, res) => {
  const query = `
    SELECT DATE(fecha) AS fecha, SUM(total) AS total
    FROM ingreso
    GROUP BY DATE(fecha)
    ORDER BY DATE(fecha) DESC;
  `;
  
  try {
    const [result] = await db.query(query);
    res.status(200).json(result);
  } catch (error) {
    console.error('Error al obtener ingresos por día:', error);
    res.status(500).json({ message: 'Error al obtener los ingresos por día' });
  }
};

// Función para obtener los ingresos por producto
const obtenerIngresosPorProducto = async (req, res) => {
  const query = `
    SELECT a.nombre, SUM(di.cantidad) AS cantidad, SUM(di.precio * di.cantidad) AS total
    FROM detalle_ingreso di
    JOIN articulo a ON di.idarticulo = a.idarticulo
    GROUP BY di.idarticulo
    ORDER BY total DESC;
  `;
  
  try {
    const [result] = await db.query(query);
    res.status(200).json(result);
  } catch (error) {
    console.error('Error al obtener ingresos por producto:', error);
    res.status(500).json({ message: 'Error al obtener los ingresos por producto' });
  }
};

// Función para obtener el ID del usuario autenticado
const obtenerIdUsuario = async (req, res) => {
  const usuarioId = req.user.idusuario;
  
  res.status(200).json({ usuarioId });
};

// Función para obtener el ID de proveedor
const obtenerIdProveedor = async (req, res) => {
  const { tipo_documento, num_documento } = req.body;
  
  const query = `
    SELECT idpersona 
    FROM persona 
    WHERE tipo_documento = ? AND num_documento = ? AND tipo_persona = 'Proveedor';
  `;
  
  try {
    const [result] = await db.query(query, [tipo_documento, num_documento]);
    
    if (result.length > 0) {
      res.status(200).json({ idproveedor: result[0].idpersona });
    } else {
      res.status(404).json({ message: 'Proveedor no encontrado' });
    }
  } catch (error) {
    console.error('Error al obtener el proveedor:', error);
    res.status(500).json({ message: 'Error al obtener el proveedor' });
  }
};

module.exports = {
  crearIngreso,
  obtenerIngresosPorDia,
  obtenerIngresosPorProducto,
  obtenerIdUsuario,
  obtenerIdProveedor
};
