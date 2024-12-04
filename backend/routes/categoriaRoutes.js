const express = require('express');
const router = express.Router();
const { insertarCategoria, obtenerCategorias, obtenerCategoriaPorId } = require('../controllers/categoriaController');
const checkRole = require('../middleware/roleMiddleware');
const verifyToken = require('../middleware/authMiddleware');
const validateCategoria = require('../middleware/validateCategoria');

// Ruta para insertar una nueva categoría (puede ser realizada por cualquier usuario con rol adecuado)
router.post('/insertar', verifyToken,validateCategoria, insertarCategoria);

// Ruta para obtener todas las categorías
router.get('/', verifyToken, obtenerCategorias);

// Ruta para obtener una categoría por ID
router.get('/:id', verifyToken, obtenerCategoriaPorId);

module.exports = router;
