// middleware/validateProduct.js
const pool = require('../db/connection'); // Asumiendo que tienes un pool de conexión a MySQL

const validateProduct = async (req, res, next) => {
  const { idcategoria, nombre, precio_venta, stock, descripcion } = req.body;

  // Validación de existencia de la categoría
  const categoryQuery = 'SELECT COUNT(*) AS count FROM categoria WHERE idcategoria = ?';
  const [categoryRows] = await pool.execute(categoryQuery, [idcategoria]);

  if (categoryRows[0].count === 0) {
    return res.status(400).json({ message: "La categoría seleccionada no existe." });
  }

  // Validación de nombre del producto
  if (!nombre || nombre.trim().length === 0) {
    return res.status(400).json({ message: "El nombre del artículo es obligatorio." });
  }

  // Validación de que el artículo no exista
  const productQuery = 'SELECT COUNT(*) AS count FROM articulo WHERE nombre = ?';
  const [productRows] = await pool.execute(productQuery, [nombre]);

  if (productRows[0].count > 0) {
    return res.status(400).json({ message: "El artículo ya existe." });
  }

  // Validación de precio y stock
  if (precio_venta <= 0) {
    return res.status(400).json({ message: "El precio de venta debe ser mayor a 0." });
  }
  if (stock < 0 || !Number.isInteger(stock)) {
    return res.status(400).json({ message: "El stock debe ser un número entero positivo." });
  }

  // Validación de la descripción
  if (descripcion && descripcion.length > 256) {
    return res.status(400).json({ message: "La descripción no puede ser mayor a 256 caracteres." });
  }

  next();
};

module.exports = validateProduct;
