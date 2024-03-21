CREATE TABLE user_auth_v1(
   login VARCHAR(50),
   mdp VARCHAR(200) NOT NULL,
   PRIMARY KEY(login)
);
INSERT INTO user_auth_v1 VALUES('secretaire1', '$2y$10$O2BnJxQq7wpub5.PIIJTnebUUWvO7nuNG5JpXOuFGbqAKSk6xEWZq');

