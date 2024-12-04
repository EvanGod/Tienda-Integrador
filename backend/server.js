require('dotenv').config();  // Cargar variables de entorno

const express = require('express');
const cors = require('cors');
const db = require('./db/connection'); // Conexión a la base de datos
const authRoutes = require('./routes/authRoutes');
const personaRoutes = require('./routes/personaRoutes');
const categoriaRoutes = require('./routes/categoriaRoutes');
const productoRoutes = require('./routes/productoRoutes');
const ventaRoutes = require('./routes/ventaRoutes');

const app = express();

// Middleware
app.use(cors());
app.use(express.json()); // Para que el servidor reciba datos JSON

// Verificación de la conexión a la base de datos
db.getConnection()
  .then(() => {
    console.log('Conexión a la base de datos establecida');
    // Iniciar servidor solo si la conexión es exitosa
    const PORT = process.env.PORT || 3000;
    app.listen(PORT, () => {
      console.log(`Servidor corriendo en puerto ${PORT}`);
    });
  })
  .catch((err) => {
    console.error('Error conectando a la base de datos', err.message);
    // Si no se puede conectar, no iniciar el servidor
    process.exit(1);
  });

// Rutas
app.use('/api/auth', authRoutes);
app.use('/api/personas', personaRoutes);
app.use('/api/categorias', categoriaRoutes);
app.use('/api/productos', productoRoutes);
app.use('/api/ventas', ventaRoutes);

// Middleware de manejo de errores (al final)
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({
    message: 'Algo salió mal',
    error: err.message
  });
});

// Middleware de log (opcional)
app.use((req, res, next) => {
  console.log(`${req.method} ${req.url}`);
  next();
});

