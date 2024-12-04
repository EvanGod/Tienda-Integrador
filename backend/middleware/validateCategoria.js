// middleware/validateCategoria.js
const pool = require('../db/connection'); // Asumiendo que tienes un pool de conexión a MySQL

const validateCategoria = async (req, res, next) => {
  const { nombre, descripcion } = req.body;

  // Validación del nombre
  if (!nombre || nombre.trim().length === 0) {
    return res.status(400).json({ message: "El nombre de la categoría es obligatorio." });
  }

  // Validación de que el nombre sea único
  const checkCategoryQuery = 'SELECT COUNT(*) AS count FROM categoria WHERE nombre = ?';
  const [categoryRows] = await pool.execute(checkCategoryQuery, [nombre]);

  if (categoryRows[0].count > 0) {
    return res.status(400).json({ message: "La categoría ya existe." });
  }

  // Validación de la descripción
  if (descripcion && descripcion.length > 256) {
    return res.status(400).json({ message: "La descripción no puede ser mayor a 256 caracteres." });
  }

  next();
};

module.exports = validateCategoria;
