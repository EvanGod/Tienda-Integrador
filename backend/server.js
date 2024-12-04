require('dotenv').config();  // Cargar variables de entorno

const express = require('express');
const bodyParser = require('body-parser');
const authRoutes = require('./routes/authRoutes');
const cors = require('cors');
const db = require('./db/connection'); // Conexión a la base de datos

const app = express();

// Middleware
app.use(cors());
app.use(bodyParser.json()); // Para que el servidor reciba datos JSON

// Verificación de la conexión a la base de datos
db.getConnection()
  .then(() => {
    console.log('Conexión a la base de datos establecida');
  })
  .catch((err) => {
    console.error('Error conectando a la base de datos', err.message);
  });

// Rutas
app.use('/api/auth', authRoutes);

// Middleware de manejo de errores
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({
    message: 'Algo salió mal',
    error: err.message
  });
});

// Iniciar servidor
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor corriendo en puerto ${PORT}`);
});
