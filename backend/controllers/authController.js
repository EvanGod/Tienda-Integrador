// controllers/authController.js
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const { getUserByEmail, comparePassword, createUser, getRoleIdByName } = require('../models/userModel');

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
    // Validar que el email no esté registrado
    const existingUser = await getUserByEmail(email);
    if (existingUser) {
      return res.status(400).json({ message: 'El correo electrónico ya está registrado' });
    }

    // Obtener idrol a partir del nombre del rol
    const idrol = await getRoleIdByName(rol);
    if (!idrol) {
      return res.status(400).json({ message: `El rol "${rol}" no existe en la base de datos` });
    }

    // Hashear la contraseña
    const hashedPassword = await bcrypt.hash(password, 10);

    // Crear el nuevo usuario
    const newUser = await createUser({
      idrol,
      nombre,
      tipo_documento,
      num_documento,
      direccion,
      telefono,
      email,
      password: hashedPassword,
    });

    // Responder con los detalles del nuevo usuario
    res.status(201).json({
      message: 'Usuario registrado exitosamente',
      user: {
        idusuario: newUser.insertId,
        nombre: newUser.nombre,
        email: newUser.email,
        rol: newUser.idrol, // o el nombre del rol si es necesario
      },
    });
  } catch (error) {
    console.error('Error en register:', error);
    res.status(500).json({ message: 'Error del servidor al registrar el usuario' });
  }
};



module.exports = { login, register };
