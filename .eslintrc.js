module.exports = {
    "env": {
        "browser": true
    },
    "extends": "eslint:recommended",
    "rules": {
        "indent": [
            "error",
            4
        ],
        "linebreak-style": [
            "error",
            "unix"
        ],
        "quotes": [
            "error",
            "single"
        ],
        "semi": [
            "error",
            "always"
        ]
    },
    "globals": {
      "window": true,
      "top": true,
      "BoomCMS": true,
      "jQuery": true,
      "$": true,
      "_": true,
      "Backbone": true,
      "moment": true,
      "Dms": true,
      "L": true,
      "jstz": true,
      "Caman": true
    }
};
