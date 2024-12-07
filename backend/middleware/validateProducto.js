const pool = require('../db/connection'); // Asumiendo que tienes un pool de conexión a MySQL

// Middleware para validar productos al crear
const validateProduct = async (req, res, next) => {
  const { idcategoria, codigo, nombre, descripcion } = req.body;

  try {
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
 // Validación de que el código sea único
 const codeQuery = 'SELECT COUNT(*) AS count FROM articulo WHERE codigo = ?';
 const [codeRows] = await pool.execute(codeQuery, [codigo]);

 if (codeRows[0].count > 0) {
   return res.status(400).json({ message: "El código del artículo ya existe. Debe ser único." });
 }

    // Validación de la descripción
    if (descripcion && descripcion.length > 256) {
      return res.status(400).json({ message: "La descripción no puede ser mayor a 256 caracteres." });
    }

    next();
  } catch (error) {
    console.error("Error en validateProduct:", error);
    res.status(500).json({ message: "Error al validar el producto en la base de datos." });
  }
};

// Middleware para validar productos al actualizar
const updateProduct = async (req, res, next) => {
  const { codigo} = req.body;
  const {idarticulo} = req.params;

  try {
    // Validación de que el código no esté duplicado en otros productos
    const codeQuery = `
      SELECT COUNT(*) AS count 
      FROM articulo 
      WHERE codigo = ? AND idarticulo != ?
    `;
    const [rows] = await pool.execute(codeQuery, [codigo, idarticulo]);

    if (rows[0].count > 0) {
      return res.status(400).json({ message: "El código del artículo ya está registrado en otro producto." });
    }

    next();
  } catch (error) {
    console.error("Error en updateProduct:", error);
    res.status(500).json({ message: "Error al validar el producto en la base de datos." });
  }
};

module.exports = { validateProduct, updateProduct };
