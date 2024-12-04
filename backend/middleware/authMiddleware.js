// middleware/authMiddleware.js
const jwt = require('jsonwebtoken');

const verifyToken = (req, res, next) => {
  const token = req.headers['authorization'];

  if (!token) {
    return res.status(403).json({ message: 'No se proporcionó un token' });
  }

  jwt.verify(token, 'secreto_jwt', (err, decoded) => {
    if (err) {
      return res.status(401).json({ message: 'Token no válido' });
    }

    req.user = decoded;
    next();
  });
};

module.exports = { verifyToken };
