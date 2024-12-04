const db = require('../db/connection'); // Asegúrate de importar la conexión de base de datos

// Función para crear un ingreso (compra) con transacción
const crearIngreso = async (req, res) => {
  const { idproveedor, productos } = req.body; // El proveedor y los productos que se están comprando

  const totalCompra = productos.reduce((total, producto) => total + (producto.cantidad * producto.precio), 0);
  const impuesto = totalCompra * 0.18; // Asumimos un impuesto del 18%
  const total = totalCompra + impuesto;

  const usuarioId = req.user.idusuario; // Obtener ID del usuario autenticado

  // Iniciar transacción
  const connection = await db.getConnection();
  await connection.beginTransaction();

  try {
    // Insertar el ingreso (compra) en la tabla ingreso
    const queryIngreso = `
      INSERT INTO ingreso (idproveedor, idusuario, tipo_comprobante, serie_comprobante, num_comprobante, fecha, impuesto, total, estado)
      VALUES (?, ?, 'Factura', ?, ?, NOW(), ?, ?, 'Activo');
    `;
    
    const valuesIngreso = [idproveedor, usuarioId, '001', '0001', impuesto, total];
    const [ingresoResult] = await connection.query(queryIngreso, valuesIngreso);
    const idIngreso = ingresoResult.insertId; // ID de la compra recién creada

    // Insertar los productos en la tabla detalle_ingreso
    const queryDetalleIngreso = `
      INSERT INTO detalle_ingreso (idingreso, idarticulo, cantidad, precio)
      VALUES (?, ?, ?, ?);
    `;
    
    for (let producto of productos) {
      const precioVenta = producto.precio; // Precio obtenido al agregar el artículo
      const descuento = precioVenta * 0.20; // 20% de descuento

      await connection.query(queryDetalleIngreso, [idIngreso, producto.idarticulo, producto.cantidad, precioVenta - descuento]);
    }

    // Si todo es correcto, confirmamos la transacción
    await connection.commit();
    res.status(201).json({ message: 'Ingreso creado con éxito' });

  } catch (error) {
    // Si ocurre un error, revertimos la transacción
    await connection.rollback();
    console.error(error);
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
    console.error(error);
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
    console.error(error);
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
    console.error(error);
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
