var wysihtml5ParserRulesInline = {
    tags: {
        div: {
            "rename_tag": "br"
        },
        p: {
            "rename_tag": "br"
        },
        p: {
            "rename_tag": "br"
        },
        br: {},
        strong: {},
        b:      {},
        i:      {},
        em:     {},
        a:      {
            set_attributes: {
                target: "_blank",
                rel:    "nofollow"
            },
            check_attributes: {
                href:   "url" // important to avoid XSS
            }
        }
      }
};