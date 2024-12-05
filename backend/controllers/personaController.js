const pool = require('../db/connection');

// Función para insertar un proveedor
const insertarProveedor = async (req, res) => {
  try {
    const { tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email } = req.body;

    const query = `
      INSERT INTO persona (tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    `;
    const [result] = await pool.query(query, [tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email]);

    res.status(201).json({ message: 'Proveedor insertado exitosamente', id: result.insertId });
  } catch (error) {
    console.error('Error al insertar proveedor:', error);
    res.status(500).json({ message: 'Error al insertar proveedor', error });
  }
};

// Función para insertar un comprador
const insertarComprador = async (req, res) => {
  try {
    const { tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email } = req.body;

    const query = `
      INSERT INTO persona (tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    `;
    const [result] = await pool.query(query, [tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email]);

    res.status(201).json({ message: 'Comprador insertado exitosamente', id: result.insertId });
  } catch (error) {
    console.error('Error al insertar comprador:', error);
    res.status(500).json({ message: 'Error al insertar comprador', error });
  }
};

// Función para obtener todos los proveedores
const obtenerProveedores = async (req, res) => {
    try {
      const query = 'SELECT * FROM persona WHERE tipo_persona = "Proveedor"';
      const [rows] = await pool.query(query);
  
      res.status(200).json(rows);
    } catch (error) {
      console.error('Error al obtener proveedores:', error);
      res.status(500).json({ message: 'Error al obtener proveedores', error });
    }
  };
  
  // Función para obtener todos los compradores
  const obtenerCompradores = async (req, res) => {
    try {
      const query = 'SELECT * FROM persona WHERE tipo_persona = "Cliente"';
      const [rows] = await pool.query(query);
  
      res.status(200).json(rows);
    } catch (error) {
      console.error('Error al obtener compradores:', error);
      res.status(500).json({ message: 'Error al obtener compradores', error });
    }
  };
  
  module.exports = { insertarProveedor, insertarComprador, obtenerProveedores, obtenerCompradores };
  