# Personal API

## Endpoints

All endpoints are available on `https://api.win97.de/api/...`

Some may be restricted by authentication while others are public..

---

### /api/song

-   [x] public

Returns the current/last song I was listening to on Spotify.

Schema on error:

```json
{
    "status": 500,
    "message": "Error message"
}
```

Schema on success:

```json
{
    "status": 200,
    "song": "Machinehead - Remastered",
    "id": "xyz",
    "artists": ["Bush"],
    "album": "Sixteen Stone (Remastered)"
}
```
