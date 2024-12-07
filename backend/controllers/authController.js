// controllers/authController.js
const jwt = require('jsonwebtoken');
const pool = require('../db/connection');
const bcrypt = require('bcryptjs');
const { getUserByEmail, comparePassword, createUser, getRoleIdByName, getUsersByRole } = require('../models/userModel');

// Función para login
const login = async (req, res) => {
  const { email, password } = req.body;

  // Validar que el email y la contraseña sean proporcionados
  if (!email || !password) {
    return res.status(400).json({ message: 'Email y contraseña son requeridos' });
  }

  try {
    const user = await getUserByEmail(email);

    if (!user) {
      return res.status(400).json({ message: 'Usuario no encontrado' });
    }

    const isPasswordValid = await comparePassword(password, user.password);

    if (!isPasswordValid) {
      return res.status(400).json({ message: 'Contraseña incorrecta' });
    }

    // Crear un token JWT con un tiempo de expiración
    const token = jwt.sign({ id: user.idusuario, role: user.idrol }, process.env.JWT_SECRET, {
      expiresIn: '1h', // El token expira en 1 hora
    });

    res.json({ message: 'Inicio de sesión exitoso', token });
  } catch (error) {
    console.error('Error en login:', error);
    res.status(500).json({ message: 'Error del servidor', error: error.message });
  }
};

const register = async (req, res) => {
  const { rol, nombre, email, password } = req.body;

  // Validación de campos requeridos
  if (!rol || !nombre || !email || !password) {
    return res.status(400).json({ message: 'Todos los campos requeridos deben ser proporcionados' });
  }

  // Propiedades opcionales
  const tipo_documento = req.body.tipo_documento || null;
  const num_documento = req.body.num_documento || null;
  const direccion = req.body.direccion || null;
  const telefono = req.body.telefono || null;

  try {
    // Validar que los campos únicos no estén registrados
    const duplicateCheckQuery = `
      SELECT COUNT(*) AS count 
      FROM usuario 
      WHERE email = ? OR telefono = ? OR nombre = ? OR num_documento = ?
    `;
    const [rows] = await pool.execute(duplicateCheckQuery, [email, telefono, nombre, num_documento]);

    if (rows[0].count > 0) {
      return res.status(400).json({
        message: 'El nombre, email, teléfono o número de documento ya están registrados',
      });
    }

    // Obtener idrol a partir del nombre del rol
    const idrol = await getRoleIdByName(rol);
    if (!idrol) {
      return res.status(400).json({ message: `El rol "${rol}" no existe en la base de datos` });
    }

    // Hashear la contraseña
    const hashedPassword = await bcrypt.hash(password, 10);

    // Crear el nuevo usuario
    const createUserQuery = `
      INSERT INTO usuario (idrol, nombre, tipo_documento, num_documento, direccion, telefono, email, password)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `;
    const [newUser] = await pool.execute(createUserQuery, [
      idrol,
      nombre,
      tipo_documento,
      num_documento,
      direccion,
      telefono,
      email,
      hashedPassword,
    ]);

    // Responder con los detalles del nuevo usuario
    res.status(201).json({
      message: 'Usuario registrado exitosamente',
      user: {
        idusuario: newUser.insertId,
        nombre,
        email,
        rol,
      },
    });
  } catch (error) {
    console.error('Error en register:', error);
    res.status(500).json({ message: 'Error del servidor al registrar el usuario' });
  }
};


const getUsers = async (req, res) => {
  try {
    const users = await getUsersByRole(); // Obtiene los usuarios ordenados por idrol
    res.json(users);
  } catch (error) {
    console.error('Error al obtener usuarios:', error);
    res.status(500).json({ message: 'Error del servidor al obtener los usuarios' });
  }
};


module.exports = { login, register, getUsers };
