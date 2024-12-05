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

// Obtener el idrol por el nombre del rol
const getRoleIdByName = async (roleName) => {
  const query = 'SELECT idrol FROM rol WHERE nombre = ?';
  const [rows] = await db.execute(query, [roleName]);
  return rows[0]?.idrol; // Retorna el idrol o undefined si no existe
};

const createUser = async ({ nombre, tipo_documento, num_documento, direccion, telefono, email, password, idrol }) => {
  const query = 'INSERT INTO usuario (nombre, tipo_documento, num_documento, direccion, telefono, email, password, idrol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
  
  const [result] = await db.execute(query, [
    nombre,
    tipo_documento,
    num_documento,
    direccion,
    telefono,
    email,
    password,
    idrol
  ]);

  return { idusuario: result.insertId, nombre, tipo_documento, num_documento, direccion, telefono, email, idrol };  // Retornar todos los datos del usuario creado
};


const getUsersByRole = async () => {
  // Consulta para obtener usuarios con idrol 2 y 3
  const query = 'SELECT * FROM usuario WHERE idrol IN (2, 3)';
  const [rows] = await db.execute(query);
  
  // Ordenar los usuarios por idrol (de menor a mayor)
  const sortedUsers = rows.sort((a, b) => a.idrol - b.idrol);
  
  return sortedUsers;
};


module.exports = { getUserByEmail, comparePassword, createUser, getRoleIdByName, getUsersByRole};
