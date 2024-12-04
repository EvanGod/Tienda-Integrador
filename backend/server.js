require('dotenv').config();
const express = require('express');
const cors = require('cors');
const db = require('./db/connection'); // Importar conexión a la base de datos

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());

// Ruta para verificar conexión con la base de datos
app.get('/db-check', async (req, res) => {
  try {
    // Consulta ajustada sin alias
    const [rows] = await db.query('SELECT NOW();');
    res.status(200).json({
      message: 'Conexión a la base de datos exitosa.',
      currentTime: rows[0]['NOW()'], // Usamos el nombre por defecto de la columna
    });
  } catch (error) {
    console.error("Database connection error:", error);
    res.status(500).json({
      message: 'Error conectando a la base de datos.',
      error: error.message,
    });
  }
});

// Ruta base
app.get('/', (req, res) => {
  res.send('Servidor funcionando');
});

// Iniciar servidor
app.listen(PORT, () => {
  console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
