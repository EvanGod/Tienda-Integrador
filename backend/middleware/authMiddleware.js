// middleware/authMiddleware.js
const jwt = require('jsonwebtoken');

const verifyToken = (req, res, next) => {
  const token = req.headers['authorization'];
  console.log('Token recibido:', token);

  if (!token) {
    return res.status(403).json({ message: 'No se proporcionó un token' });
  }
  const tokenWithoutBearer = token.split(' ')[1]; // Extrae el token real
  jwt.verify(tokenWithoutBearer, process.env.JWT_SECRET, (err, decoded) => {
    if (err) {
      return res.status(401).json({ message: 'Token no válido' });
    }
    console.log('Token decodificado:', decoded);
    req.user = decoded; // Aquí debería ir el decoded que incluye el rol
    next();
  });
};

module.exports =  verifyToken ;
