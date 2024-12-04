// middleware/validatePersona.js
const pool = require('../db/connection'); // Asumiendo que tienes un pool de conexión a MySQL

const validatePersona = async (req, res, next) => {
  const { tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email } = req.body;

  // Validación del tipo de persona
  if (!tipo_persona || (tipo_persona !== 'Proveedor' && tipo_persona !== 'Cliente')) {
    return res.status(400).json({ message: "El tipo de persona debe ser 'Proveedor' o 'Cliente'." });
  }

  // Validación del nombre
  if (!nombre || nombre.trim().length === 0) {
    return res.status(400).json({ message: "El nombre de la persona es obligatorio." });
  }

  // Validación de documento
  if (!tipo_documento || !num_documento) {
    return res.status(400).json({ message: "El tipo y número de documento son obligatorios." });
  }

  // Validación de teléfono (opcional)
  if (telefono && !/^\d{9,15}$/.test(telefono)) {
    return res.status(400).json({ message: "El teléfono debe ser un número válido de entre 9 y 15 dígitos." });
  }

  // Validación de email (opcional)
  if (email && !/^\S+@\S+\.\S+$/.test(email)) {
    return res.status(400).json({ message: "El correo electrónico no es válido." });
  }

  // Validación de que el documento no esté duplicado
  const checkPersonaQuery = 'SELECT COUNT(*) AS count FROM persona WHERE num_documento = ?';
  const [personaRows] = await pool.execute(checkPersonaQuery, [num_documento]);

  if (personaRows[0].count > 0) {
    return res.status(400).json({ message: "Ya existe una persona con ese número de documento." });
  }

  next();
};

module.exports = validatePersona;
