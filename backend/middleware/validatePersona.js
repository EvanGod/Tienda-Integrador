// middleware/validatePersona.js
const pool = require('../db/connection'); // Asumiendo que tienes un pool de conexión a MySQL

const validatePersona = async (req, res, next) => {
  const { tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email } = req.body;

  try {
    // Validación del tipo de persona
    if (!tipo_persona || (tipo_persona !== 'Proveedor' && tipo_persona !== 'Cliente')) {
      return res.status(400).json({ message: "El tipo de persona debe ser 'Proveedor' o 'Cliente'." });
    }

    // Validación del nombre
    if (!nombre || nombre.trim().length === 0) {
      return res.status(400).json({ message: "El nombre de la persona es obligatorio." });
    }

    // Validación del teléfono (opcional)
    if (telefono && !/^\d{9,15}$/.test(telefono)) {
      return res.status(400).json({ message: "El teléfono debe ser un número válido de entre 9 y 15 dígitos." });
    }

    // Validación del email (opcional)
    if (email && !/^\S+@\S+\.\S+$/.test(email)) {
      return res.status(400).json({ message: "El correo electrónico no es válido." });
    }

    // Validaciones en la base de datos para verificar duplicados
    const checkDuplicateQuery = `
      SELECT 
        COUNT(*) AS count 
      FROM 
        persona 
      WHERE 
        num_documento = ? OR 
        email = ? OR 
        telefono = ? OR 
        nombre = ?
    `;
    const [rows] = await pool.execute(checkDuplicateQuery, [num_documento, email, telefono, nombre]);

    if (rows[0].count > 0) {
      return res.status(400).json({ 
        message: "El número de documento, email, teléfono o nombre ya está registrado en otra persona." 
      });
    }

    // Si todas las validaciones pasan, continúa al siguiente middleware/controlador
    next();
  } catch (error) {
    console.error('Error en la validación de persona:', error);
    res.status(500).json({ message: 'Error interno del servidor durante la validación.' });
  }
};

module.exports = validatePersona;
