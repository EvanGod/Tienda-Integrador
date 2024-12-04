// controllers/authController.js
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const { getUserByEmail, comparePassword } = require('../models/userModel');

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

module.exports = { login };
