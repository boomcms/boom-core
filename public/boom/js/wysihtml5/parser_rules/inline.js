var wysihtml5ParserRulesInline = {
  tags: {
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