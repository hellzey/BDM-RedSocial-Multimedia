CREATE VIEW Vista_PublicacionesConResumen AS
SELECT 
  p.publiID, 
  p.descripcion, 
  p.fechacreacion,
  COALESCE(r.total_reacciones, 0) AS total_reacciones,
  COALESCE(c.total_comentarios, 0) AS total_comentarios,
  p.usuarioID
FROM Publicaciones p
LEFT JOIN (
  SELECT publiID, COUNT(*) AS total_reacciones
  FROM Reacciones
  GROUP BY publiID
) r ON p.publiID = r.publiID
LEFT JOIN (
  SELECT publiID, COUNT(*) AS total_comentarios
  FROM Comentarios
  GROUP BY publiID
) c ON p.publiID = c.publiID;
