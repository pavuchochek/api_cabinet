CREATE TABLE user_auth_v1(
   login VARCHAR(50),
   mdp VARCHAR(50) NOT NULL,
   PRIMARY KEY(login)
);

CREATE TABLE user_auth_v2(
   login VARCHAR(50),
   mdp VARCHAR(50) NOT NULL,
   id_auth VARCHAR(50) NOT NULL,
   role VARCHAR(50),
   PRIMARY KEY(login),
   UNIQUE(id_auth)
);
