const pool = require('../db/connection');

// Crear un nuevo producto
const crearProducto = async (producto) => {
  const { idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado } = producto;
  const query = `INSERT INTO articulo (idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)`;
  const [result] = await pool.execute(query, [idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado]);
  return result;
};

// Obtener todos los productos
const obtenerProductos = async () => {
  const query = `SELECT * FROM articulo WHERE estado = 1`;
  const [result] = await pool.execute(query);
  return result;
};

// Obtener un producto por ID
const obtenerProductoPorId = async (idarticulo) => {
  const query = `SELECT * FROM articulo WHERE idarticulo = ? AND estado = 1`;
  const [result] = await pool.execute(query, [idarticulo]);
  return result;
};

// Actualizar un producto
const actualizarProducto = async (idarticulo, producto) => {
  const { idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado } = producto;
  const query = `UPDATE articulo SET idcategoria = ?, codigo = ?, nombre = ?, precio_venta = ?, stock = ?, descripcion = ?, estado = ? 
                 WHERE idarticulo = ?`;
  const [result] = await pool.execute(query, [idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado, idarticulo]);
  return result;
};

// Eliminar un producto (cambiar estado)
const eliminarProducto = async (idarticulo) => {
  const query = `UPDATE articulo SET estado = 0 WHERE idarticulo = ?`;
  const [result] = await pool.execute(query, [idarticulo]);
  return result;
};

module.exports = { crearProducto, obtenerProductos, obtenerProductoPorId, actualizarProducto, eliminarProducto };
