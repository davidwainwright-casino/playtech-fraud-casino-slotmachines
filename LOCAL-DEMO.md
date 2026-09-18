# Local-only runtime

Start everything:

```bash
docker compose up --build
```

On the same Wi-Fi network, open this on your phone:

```text
http://YOUR-COMPUTER-LAN-IP:8000/play
```

For example: `http://192.168.1.25:8000/play`.

Tap **Open game**, then **Initialize** and **Spin**. The page uses a local deterministic game and fake funds only. Your computer firewall must allow TCP port 8000, and the phone must be on the same network.
