DELIMITER //

CREATE FUNCTION fn_contarLikes(publi INT) RETURNS INT
BEGIN
  DECLARE total INT;
  SELECT COUNT(*) INTO total FROM Reacciones WHERE publiID = publi AND tipo = 1;
  RETURN total;
END //

DELIMITER ;