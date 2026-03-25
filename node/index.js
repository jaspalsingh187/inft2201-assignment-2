import http from "http";
import fs from "fs";
import jwt from "jsonwebtoken";

const JWT_SECRET = "jaspal_random_secret_2026_8472";

http
  .createServer((req, res) => {
    if (req.method === "GET") {
      res.writeHead(200, { "Content-Type": "text/plain" });
      res.end("Hello Apache!\n");

      return;
    }

    if (req.method === "POST") {
      if (req.url === "/login") {
        let body = "";
        req.on("data", (chunk) => {
          body += chunk;
        });
        req.on("end", () => {
          try {
            body = JSON.parse(body);

            const users = fs.readFileSync("./users.txt", "utf8").trim().split("\n");

            let foundUser = null;

            for (const line of users) {
              const [username, password, userId, role] = line.trim().split(",");

              if (username === body.username) {
                foundUser = { userId, username, password, role };
                break;
              }
            }

            if (!foundUser) {
              res.writeHead(404, { "Content-Type": "text/plain" });
              res.end(`${body.username} not found\n`);
              return;
            }

            if (foundUser.password !== body.password) {
              res.writeHead(401, { "Content-Type": "text/plain" });
              res.end("Invalid password\n");
              return;
            }

            const token = jwt.sign(
              {
                userId: parseInt(foundUser.userId),
                role: foundUser.role
              },
              JWT_SECRET,
              { expiresIn: "1h" }
            );

            res.writeHead(200, { "Content-Type": "application/json" });
            res.end(JSON.stringify({ token }));
          } catch (err) {
            console.log(err);
            res.writeHead(500, { "Content-Type": "text/plain" });
            res.end("Server error\n");
          }
        });
      }

      return;
    }

    res.writeHead(404, { "Content-Type": "text/plain" });
    res.end("Not found\n");
  })
  .listen(8000);

console.log("listening on port 8000");