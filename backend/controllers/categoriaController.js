const pool = require('../db/connection');

// Función para insertar una nueva categoría
const insertarCategoria = async (req, res) => {
  try {
    const { nombre, descripcion } = req.body;

    const query = `
      INSERT INTO categoria (nombre, descripcion)
      VALUES (?, ?)
    `;
    const [result] = await pool.query(query, [nombre, descripcion]);

    res.status(201).json({ message: 'Categoría insertada exitosamente', id: result.insertId });
  } catch (error) {
    console.error('Error al insertar categoría:', error);
    res.status(500).json({ message: 'Error al insertar categoría', error });
  }
};

// Función para obtener todas las categorías
const obtenerCategorias = async (req, res) => {
  try {
    const query = 'SELECT * FROM categoria WHERE estado = 1';
    const [rows] = await pool.query(query);

    res.status(200).json(rows);
  } catch (error) {
    console.error('Error al obtener categorías:', error);
    res.status(500).json({ message: 'Error al obtener categorías', error });
  }
};

// Función para obtener una categoría específica por ID
const obtenerCategoriaPorId = async (req, res) => {
  const { id } = req.params;

  try {
    const query = 'SELECT * FROM categoria WHERE idcategoria = ? AND estado = 1';
    const [rows] = await pool.query(query, [id]);

    if (rows.length === 0) {
      return res.status(404).json({ message: 'Categoría no encontrada' });
    }

    res.status(200).json(rows[0]);
  } catch (error) {
    console.error('Error al obtener categoría:', error);
    res.status(500).json({ message: 'Error al obtener categoría', error });
  }
};

module.exports = { insertarCategoria, obtenerCategorias, obtenerCategoriaPorId };
