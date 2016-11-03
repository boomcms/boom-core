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
      "wysihtml5": true,
      "_": true,
      "Backbone": true,
      "moment": true,
      "Dms": true,
      "L": true,
      "wysihtml5ParserRules": true,
      "wysihtml5ParserRulesInline": true,
      "jstz": true,
      "Caman": true,
    }
};
