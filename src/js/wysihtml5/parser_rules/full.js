var wysihtml5ParserRules = {
    /**
     * CSS Class white-list
     * Following CSS classes won't be removed when parsed by the wysihtml5 HTML parser
     */
    "classes": {
		'cta' : true
	},

    "type_definitions": {

        "alignment_object": {
            "classes": {},
            "styles": {}
        },

        "valid_image_src": {
            "attrs": {
                "src": /^[^data\:]/i
            }
        },

        "text_color_object": {
          "styles": {}
        },

        "text_fontsize_object": {
          "styles": {}
        },

        "text_formatting_object": {
            "classes": {
		'cta' : 1,
		'b-asset-embed' : 1
	    }
        }
    },

    /**
     * Tag list
     *
     * The following options are available:
     *
     *    - add_class:        converts and deletes the given HTML4 attribute (align, clear, ...) via the given method to a css class
     *                        The following methods are implemented in wysihtml5.dom.parse:
     *                          - align_text:  converts align attribute values (right/left/center/justify) to their corresponding css class "wysiwyg-text-align-*")
     *                            <p align="center">foo</p> ... becomes ... <p> class="wysiwyg-text-align-center">foo</p>
     *                          - clear_br:    converts clear attribute values left/right/all/both to their corresponding css class "wysiwyg-clear-*"
     *                            <br clear="all"> ... becomes ... <br class="wysiwyg-clear-both">
     *                          - align_img:    converts align attribute values (right/left) on <img> to their corresponding css class "wysiwyg-float-*"
     *
     *    - remove:             removes the element and its content
     *
     *    - unwrap              removes element but leaves content
     *
     *    - rename_tag:         renames the element to the given tag
     *
     *    - set_class:          adds the given class to the element (note: make sure that the class is in the "classes" white list above)
     *
     *    - set_attributes:     sets/overrides the given attributes
     *
     *    - check_attributes:   checks the given HTML attribute via the given method
     *                            - url:            allows only valid urls (starting with http:// or https://)
     *                            - src:            allows something like "/foobar.jpg", "http://google.com", ...
     *                            - href:           allows something like "mailto:bert@foo.com", "http://google.com", "/foobar.jpg"
     *                            - alt:            strips unwanted characters. if the attribute is not set, then it gets set (to ensure valid and compatible HTML)
     *                            - numbers:  ensures that the attribute only contains numeric characters
     *                            - any:            allows anything to pass
     */
    "tags": {
        "tr": {},
        "strike": {
            "unwrap": 1
        },
        "form": {
            "unwrap": 1
        },
        "rt": {
            "rename_tag": "span"
        },
        "code": {},
        "acronym": {
            "rename_tag": "span"
        },
        "br": {
            "add_class": {
                "clear": "clear_br"
            }
        },
        "details": {
            "unwrap": 1
        },
        "h4": {
            "unwrap": 1
        },
        "em": {},
        "title": {
            "remove": 1
        },
        "multicol": {
            "unwrap": 1
        },
        "figure": {
            "unwrap": 1
        },
        "xmp": {
            "unwrap": 1
        },
        "small": {
            "unwrap": 1
        },
        "area": {
            "remove": 1
        },
        "time": {
            "unwrap": 1
        },
        "dir": {
            "rename_tag": "ul"
        },
        "bdi": {
            "unwrap": 1
        },
        "command": {
            "unwrap": 1
        },
        "ul": {},
        "progress": {
            "rename_tag": "span"
        },
        "dfn": {
            "unwrap": 1
        },
        "iframe": {},
        "figcaption": {
            "unwrap": 1
        },
        "a": {
            "check_attributes": {
                "href": "any", // if you compiled master manually then change this from 'url' to 'href'
                "target": "any"
            }
        },
        "img": {
            "check_attributes": {
                "width": "numbers",
                "alt": "alt",
                "src": "href", // if you compiled master manually then change this from 'url' to 'src'
                "height": "numbers"
            }
        },
        "rb": {
            "unwrap": 1
        },
        "footer": {
            "rename_tag": "div"
        },
        "noframes": {
            "remove": 1
        },
        "abbr": {
            "unwrap": 1
        },
        "u": {
	    "unwrap": 1
	},
        "bgsound": {
            "remove": 1
        },
        "sup": {},
        "address": {
            "unwrap": 1
        },
        "basefont": {
            "remove": 1
        },
        "nav": {
            "unwrap": 1
        },
        "h1": {
		"rename_tag" : 'h2'
        },
        "head": {
            "unwrap": 1
        },
        "tbody": {},
        "dd": {
            "unwrap": 1
        },
        "s": {
            "unwrap": 1
        },
        "li": {},
        "td": {},
        "object": {
            "remove": 1
        },

        "div": {
            "one_of_type": {
                "alignment_object": 1,
            },
            "remove_action": "unwrap",
            "keep_styles": {},
            "add_class": {
                "align": "align_text"
            }
        },

        "option": {
            "remove":1
        },
        "select": {
            "remove":1
        },
        "i": {},
        "track": {
            "remove": 1
        },
        "wbr": {
            "remove": 1
        },
        "fieldset": {
            "unwrap": 1
        },
        "big": {
            "unwrap": 1
        },
        "button": {
            "unwrap": 1
        },
        "noscript": {
            "remove": 1
        },
        "svg": {
            "remove": 1
        },
        "input": {
            "remove": 1
        },
        "table": {},
        "keygen": {
            "remove": 1
        },
        "h5": {
            "unwrap": 1
        },
        "meta": {
            "remove": 1
        },
        "map": {
            "remove": 1
        },
        "isindex": {
            "remove": 1
        },
        "mark": {
            "unwrap": 1
        },
        "caption": {
           "unwrap": 1
        },
        "tfoot": {},
        "base": {
            "remove": 1
        },
        "video": {},
        "strong": {},
        "canvas": {
            "remove": 1
        },
        "output": {
            "unwrap": 1
        },
        "marquee": {
            "unwrap": 1
        },
        "b": {},
        "q": {
            "check_attributes": {
                "cite": "url"
            }
        },
        "applet": {
            "remove": 1
        },
        "span": {
            "unwrap": 1
        },
        "rp": {
            "unwrap": 1
        },
        "spacer": {
            "remove": 1
        },
        "source": {
            "remove": 1
        },
        "aside": {
            "rename_tag": "div"
        },
        "frame": {
            "remove": 1
        },
        "section": {
            "rename_tag": "div"
        },
        "body": {
            "unwrap": 1
        },
        "ol": {},
        "nobr": {
            "unwrap": 1
        },
        "html": {
            "unwrap": 1
        },
        "summary": {
            "unwrap": 1
        },
        "var": {
            "unwrap": 1
        },
        "del": {
            "unwrap": 1
        },
        "blockquote": {},
        "style": {
            "remove": 1
        },
        "device": {
            "remove": 1
        },
        "meter": {
            "unwrap": 1
        },
        "h3": {},
        "textarea": {
            "unwrap": 1
        },
        "embed": {
            "remove": 1
        },
        "hgroup": {
            "unwrap": 1
        },
        "font": {
            "unwrap" : 1,
        },
        "tt": {
            "unwrap": 1
        },
        "noembed": {
            "remove": 1
        },
        "thead": {},
        "blink": {
            "unwrap": 1
        },
        "plaintext": {
            "unwrap": 1
        },
        "xml": {
            "remove": 1
        },
        "h6": {
            "unwrap" : 1
        },
        "param": {
            "remove": 1
        },
        "th": {
            "check_attributes": {
                "rowspan": "numbers",
                "colspan": "numbers"
            }
        },
        "legend": {
            "unwrap": 1
        },
        "hr": {},
        "label": {
            "unwrap": 1
        },
        "dl": {
            "unwrap": 1
        },
        "kbd": {
            "unwrap": 1
        },
        "listing": {
            "unwrap": 1
        },
        "dt": {
            "unwrap": 1
        },
        "nextid": {
            "remove": 1
        },
        "pre": {},
        "center": {
            "unwrap" : 1
        },
        "audio": {
            "remove": 1
        },
        "datalist": {
            "unwrap": 1
        },
        "samp": {
            "unwrap": 1
        },
        "col": {
            "remove": 1
        },
        "article": {
            "rename_tag": "div"
        },
        "cite": {},
        "link": {
            "remove": 1
        },
        "script": {
            "remove": 1
        },
        "bdo": {
            "unwrap": 1
        },
        "menu": {
            "rename_tag": "ul"
        },
        "colgroup": {
            "remove": 1
        },
        "ruby": {
            "unwrap": 1
        },
        "h2": {},
        "ins": {
            "unwrap": 1
        },
        "p": {},
        "sub": {},
        "comment": {
            "remove": 1
        },
        "frameset": {
            "remove": 1
        },
        "optgroup": {
            "unwrap": 1
        },
        "header": {
            "rename_tag": "div"
        }
    }
};