<<<<<<< HEAD
--------------------------------------------
-- Serviços (Windows)
--------------------------------------------

-- Websocket
php D:\Web\tcc\index.php servidor websocket

-- SP, LP e SSE
php D:\Web\tcc\index.php servidor generico

-- MongoDB (windows)
D:\Web\mongodb\mongod.exe --dbpath D:\Web\mongodb\data

--------------------------------------------
-- Serviços (Linux)
--------------------------------------------

-- Websocket
php /var/www/tcc/index.php servidor websocket

-- SP, LP e SSE
php /var/www/tcc/index.php servidor generico

--------------------------------------------
-- Dados
--------------------------------------------

-- Usuários
db.usuario.insert({"imagem" : "admin.png", "login" : "admin", "nome" : "Administrador", "senha" : "21232f297a57a5a743894a0e4a801fc3"});
db.usuario.insert({"imagem" : "servico_canal.png", "login" : "servico_canal", "nome" : "Serviço de canal", "senha" : "21232f297a57a5a743894a0e4a801fc3"});
db.usuario.insert({"imagem" : "sistema.png", "login" : "sistema", "nome" : "Sistema", "senha" : "21232f297a57a5a743894a0e4a801fc3"});
db.usuario.insert({"imagem" : "chrome.png", "login" : "chrome", "nome" : "Google Chrome", "senha" : "21232f297a57a5a743894a0e4a801fc3"});
db.usuario.insert({"imagem" : "firefox.png", "login" : "firefox", "nome" : "Firefox", "senha" : "21232f297a57a5a743894a0e4a801fc3"});
db.usuario.insert({"imagem" : "safari.png", "login" : "safari", "nome" : "Safari", "senha" : "21232f297a57a5a743894a0e4a801fc3"});
db.usuario.insert({"imagem" : "opera.png", "login" : "opera", "nome" : "Opera", "senha" : "21232f297a57a5a743894a0e4a801fc3"});

-- Canais
db.canal.insert({"imagem" : "php.png", "nome" : "php", "topico" : "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged"});
db.canal.insert({"imagem" : "html5.png", "nome" : "html5", "topico" : "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged"});
db.canal.insert({"imagem" : "javascript.png", "nome" : "javascript", "topico" : "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged"});
db.canal.insert({"imagem" : "css.png", "nome" : "css", "topico" : "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged"});
db.canal.insert({"imagem" : "mongodb.png", "nome" : "mongodb", "topico" : "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged"});
=======
tcc
===

Trabalho de conclusão de Curso - Anhanguera 2013
>>>>>>> c846c225da44bfa81ec9a701a6d8511ebf1429ce