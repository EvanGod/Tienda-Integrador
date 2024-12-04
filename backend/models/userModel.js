// models/userModel.js
const db = require('../db/connection'); // Asegúrate de que este archivo de conexión esté correcto.
const bcrypt = require('bcryptjs');

const getUserByEmail = async (email) => {
  const query = 'SELECT * FROM usuario WHERE email = ?';
  const [rows] = await db.execute(query, [email]);
  return rows[0]; // Devolver el primer usuario encontrado
};

const comparePassword = async (password, hash) => {
  return bcrypt.compare(password, hash);
};

module.exports = { getUserByEmail, comparePassword };
