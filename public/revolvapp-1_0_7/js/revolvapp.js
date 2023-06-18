/*
    Revolvapp
	Version 1.0.7
	Updated: May 24, 2019

	http://imperavi.com/revolvapp/

	Copyright (c) 2009-2019, Imperavi LLC.
	License:
*/
(function() {
var Ajax = {};

Ajax.settings = {};
Ajax.post = function(options) { return new AjaxRequest('post', options); };
Ajax.get = function(options) { return new AjaxRequest('get', options); };

var AjaxRequest = function(method, options)
{
    var defaults = {
        method: method,
        url: '',
        before: function() {},
        success: function() {},
        error: function() {},
        data: false,
        async: true,
        headers: {}
    };

    this.p = this.extend(defaults, options);
    this.p = this.extend(this.p, Ajax.settings);
    this.p.method = this.p.method.toUpperCase();

    this.prepareData();

    this.xhr = new XMLHttpRequest();
    this.xhr.open(this.p.method, this.p.url, this.p.async);

    this.setHeaders();

    var before = (typeof this.p.before === 'function') ? this.p.before(this.xhr) : true;
    if (before !== false)
    {
        this.send();
    }
};

AjaxRequest.prototype = {
    extend: function(obj1, obj2)
    {
        if (obj2) for (var name in obj2) { obj1[name] = obj2[name]; }
        return obj1;
    },
    prepareData: function()
    {
        if (this.p.method === 'POST' && !this.isFormData()) this.p.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        if (typeof this.p.data === 'object' && !this.isFormData()) this.p.data = this.toParams(this.p.data);
        if (this.p.method === 'GET') this.p.url = (this.p.data) ? this.p.url + '?' + this.p.data : this.p.url;
    },
    setHeaders: function()
    {
        this.xhr.setRequestHeader('X-Requested-With', this.p.headers['X-Requested-With'] || 'XMLHttpRequest');
        for (var name in this.p.headers)
        {
            this.xhr.setRequestHeader(name, this.p.headers[name]);
        }
    },
    isFormData: function()
    {
        return (typeof window.FormData !== 'undefined' && this.p.data instanceof window.FormData);
    },
    isComplete: function()
    {
        return !(this.xhr.status < 200 || this.xhr.status >= 300 && this.xhr.status !== 304);
    },
    send: function()
    {
        if (this.p.async)
        {
            this.xhr.onload = this.loaded.bind(this);
            this.xhr.send(this.p.data);
        }
        else
        {
            this.xhr.send(this.p.data);
            this.loaded.call(this);
        }
    },
    loaded: function()
    {
        if (this.isComplete())
        {
            var response = this.xhr.response;
            var json = this.parseJson(response);
            response = (json) ? json : response;

            if (typeof this.p.success === 'function') this.p.success(response, this.xhr);
        }
        else
        {
            if (typeof this.p.error === 'function') this.p.error(this.xhr.statusText);
        }
    },
    parseJson: function(str)
    {
        try {
            var o = JSON.parse(str);
            if (o && typeof o === 'object')
            {
                return o;
            }

        } catch (e) {}

        return false;
    },
    toParams: function (obj)
    {
        return Object.keys(obj).map(
            function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(obj[k]); }
        ).join('&');
    }
};
var DomCache = [0];
var DomExpando = 'data' + new Date().getTime();
var DomHClass = 'is-hidden';
var DomHMClass = 'is-hidden-mobile';

var Dom = function(selector, context)
{
    return this.parse(selector, context);
};

Dom.ready = function(fn)
{
    if (document.readyState != 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
};

Dom.prototype = {
    get dom()
    {
        return true;
    },
    get length()
    {
        return this.nodes.length;
    },
    parse: function(selector, context)
    {
        var nodes;
        var reHtmlTest = /^\s*<(\w+|!)[^>]*>/;

        if (!selector)
        {
            nodes = [];
        }
        else if (selector.dom)
        {
            this.nodes = selector.nodes;
            return selector;
        }
        else if (typeof selector !== 'string')
        {
            if (selector.nodeType && selector.nodeType === 11)
            {
                nodes = selector.childNodes;
            }
            else
            {
                nodes = (selector.nodeType || selector === window) ? [selector] : selector;
            }
        }
        else if (reHtmlTest.test(selector))
        {
            nodes = this.create(selector);
        }
        else
        {
            nodes = this._query(selector, context);
        }

        this.nodes = this._slice(nodes);
    },
    create: function(html)
    {
        if (/^<(\w+)\s*\/?>(?:<\/\1>|)$/.test(html))
        {
            return [document.createElement(RegExp.$1)];
        }

        var elements = [];
        var container = document.createElement('div');
        var children = container.childNodes;

        container.innerHTML = html;

        for (var i = 0, l = children.length; i < l; i++)
        {
            elements.push(children[i]);
        }

        return elements;
    },

    // add
    add: function(nodes)
    {
        this.nodes = this.nodes.concat(this._toArray(nodes));
    },

    // get
    get: function(index)
    {
        return this.nodes[(index || 0)] || false;
    },
    getAll: function()
    {
        return this.nodes;
    },
    eq: function(index)
    {
        return new Dom(this.nodes[index]);
    },
    first: function()
    {
        return new Dom(this.nodes[0]);
    },
    last: function()
    {
        return new Dom(this.nodes[this.nodes.length - 1]);
    },
    contents: function()
    {
        return this.get().childNodes;
    },

    // loop
    each: function(callback)
    {
        var len = this.nodes.length;
        for (var i = 0; i < len; i++)
        {
            callback.call(this, (this.nodes[i].dom) ? this.nodes[i].get() : this.nodes[i], i);
        }

        return this;
    },

    // traversing
    is: function(selector)
    {
        return (this.filter(selector).length > 0);
    },
    filter: function (selector)
    {
        var callback;
        if (selector === undefined)
        {
            return this;
        }
        else if (typeof selector === 'function')
        {
            callback = selector;
        }
        else
        {
            callback = function(node)
            {
                if (selector instanceof Node)
                {
                    return (selector === node);
                }
                else if (selector && selector.dom)
                {
                    return ((selector.nodes).indexOf(node) !== -1);
                }
                else
                {
                    node.matches = node.matches || node.msMatchesSelector || node.webkitMatchesSelector;
                    return (node.nodeType === 1) ? node.matches(selector || '*') : false;
                }
            };
        }

        return new Dom(this.nodes.filter(callback));
    },
    not: function(filter)
    {
        return this.filter(function(node)
        {
            return !new Dom(node).is(filter || true);
        });
    },
    find: function(selector)
    {
        var nodes = [];
        this.each(function(node)
        {
            var ns = this._query(selector || '*', node);
            for (var i = 0; i < ns.length; i++)
            {
                nodes.push(ns[i]);
            }
        });

        return new Dom(nodes);
    },
    children: function(selector)
    {
        var nodes = [];
        this.each(function(node)
        {
            if (node.children)
            {
                var ns = node.children;
                for (var i = 0; i < ns.length; i++)
                {
                    nodes.push(ns[i]);
                }
            }
        });

        return new Dom(nodes).filter(selector);
    },
    parent: function(selector)
    {
        var nodes = [];
        this.each(function(node)
        {
            if (node.parentNode) nodes.push(node.parentNode);
        });

        return new Dom(nodes).filter(selector);
    },
    parents: function(selector, context)
    {
        context = this._getContext(context);

        var nodes = [];
        this.each(function(node)
        {
            var parent = node.parentNode;
            while (parent && parent !== context)
            {
                if (selector)
                {
                    if (new Dom(parent).is(selector)) { nodes.push(parent); }
                }
                else
                {
                    nodes.push(parent);
                }

                parent = parent.parentNode;
            }
        });

        return new Dom(nodes);
    },
    closest: function(selector, context)
    {
        context = this._getContext(context);
        selector = (selector.dom) ? selector.get() : selector;

        var nodes = [];
        var isNode = (selector && selector.nodeType);
        this.each(function(node)
        {
            do {
                if ((isNode && node === selector) || new Dom(node).is(selector)) return nodes.push(node);
            } while ((node = node.parentNode) && node !== context);
        });

        return new Dom(nodes);
    },
    next: function(selector)
    {
         return this._getSibling(selector, 'nextSibling');
    },
    nextElement: function(selector)
    {
        return this._getSibling(selector, 'nextElementSibling');
    },
    prev: function(selector)
    {
        return this._getSibling(selector, 'previousSibling');
    },
    prevElement: function(selector)
    {
        return this._getSibling(selector, 'previousElementSibling');
    },

    // css
    css: function(name, value)
    {
        if (value === undefined && (typeof name !== 'object'))
        {
            var node = this.get();
            if (name === 'width' || name === 'height')
            {
                return (node.style) ? this._getHeightOrWidth(name, node, false) + 'px' : undefined;
            }
            else
            {
                return (node.style) ? getComputedStyle(node, null)[name] : undefined;
            }
        }

        // set
        return this.each(function(node)
        {
            var obj = {};
            if (typeof name === 'object') obj = name;
            else obj[name] = value;

            for (var key in obj)
            {
                if (node.style) node.style[key] = obj[key];
            }
        });
    },

    // attr
    attr: function(name, value, data)
    {
        data = (data) ? 'data-' : '';

        if (value === undefined && (typeof name !== 'object'))
        {
            var node = this.get();
            if (node && node.nodeType !== 3)
            {
                return (name === 'checked') ? node.checked : this._getBooleanFromStr(node.getAttribute(data + name));
            }
            else return;
        }

        // set
        return this.each(function(node)
        {
            var obj = {};
            if (typeof name === 'object') obj = name;
            else obj[name] = value;

            for (var key in obj)
            {
                if (node.nodeType !== 3)
                {
                    if (key === 'checked') node.checked = obj[key];
                    else node.setAttribute(data + key, obj[key]);
                }
            }
        });
    },
    data: function(name, value)
    {
        if (name === undefined)
        {
            var reDataAttr = /^data\-(.+)$/;
            var attrs = this.get().attributes;

            var data = {};
            var replacer = function (g) { return g[1].toUpperCase(); };

            for (var key in attrs)
            {
                if (attrs[key] && reDataAttr.test(attrs[key].nodeName))
                {
                    var dataName = attrs[key].nodeName.match(reDataAttr)[1];
                    var val = attrs[key].value;
                    dataName = dataName.replace(/-([a-z])/g, replacer);

                    if (this._isObjectString(val)) val = this._toObject(val);
                    else val = (this._isNumber(val)) ? parseFloat(val) : this._getBooleanFromStr(val);

                    data[dataName] = val;
                }
            }

            return data;
        }

        return this.attr(name, value, true);
    },
    val: function(value)
    {
        if (value === undefined)
        {
            var el = this.get();
            if (el.type && el.type === 'checkbox') return el.checked;
            else return el.value;
        }

        return this.each(function(node)
        {
            node.value = value;
        });
    },
    removeAttr: function(value)
    {
        return this.each(function(node)
        {
            var rmAttr = function(name) { if (node.nodeType !== 3) node.removeAttribute(name); };
            value.split(' ').forEach(rmAttr);
        });
    },
    removeData: function(value)
    {
        return this.each(function(node)
        {
            var rmData = function(name) { if (node.nodeType !== 3) node.removeAttribute('data-' + name); };
            value.split(' ').forEach(rmData);
        });
    },

    // dataset/dataget
    dataset: function(key, value)
    {
        return this.each(function(node)
        {
            DomCache[this.dataindex(node)][key] = value;
        });
    },
    dataget: function(key)
    {
        return DomCache[this.dataindex(this.get())][key];
    },
    dataindex: function(el)
    {
        var cacheIndex = el[DomExpando];
        var nextCacheIndex = DomCache.length;

        if (!cacheIndex)
        {
            cacheIndex = el[DomExpando] = nextCacheIndex;
            DomCache[cacheIndex] = {};
        }

        return cacheIndex;
    },


    // class
    addClass: function(value)
    {
        return this._eachClass(value, 'add');
    },
    removeClass: function(value)
    {
        return this._eachClass(value, 'remove');
    },
    toggleClass: function(value)
    {
        return this._eachClass(value, 'toggle');
    },
    hasClass: function(value)
    {
        return this.nodes.some(function(node)
        {
            return (node.classList) ? node.classList.contains(value) : false;
        });
    },

    // html & text
    empty: function()
    {
        return this.each(function(node)
        {
            node.innerHTML = '';
        });
    },
    html: function(html)
    {
        return (html === undefined) ? (this.get().innerHTML || '') : this.empty().append(html);
    },
    text: function(text)
    {
        return (text === undefined) ? (this.get().textContent || '') : this.each(function(node) { node.textContent = text; });
    },

    // manipulation
    after: function(html)
    {
        return this._inject(html, function(frag, node)
        {
            if (typeof frag === 'string')
            {
                node.insertAdjacentHTML('afterend', frag);
            }
            else
            {
                var elms = (frag instanceof Node) ? [frag] : this._toArray(frag).reverse();
                for (var i = 0; i < elms.length; i++)
                {
                    node.parentNode.insertBefore(elms[i], node.nextSibling);
                }
            }

            return node;

        });
    },
    before: function(html)
    {
        return this._inject(html, function(frag, node)
        {
            if (typeof frag === 'string')
            {
                node.insertAdjacentHTML('beforebegin', frag);
            }
            else
            {
                var elms = (frag instanceof Node) ? [frag] : this._toArray(frag);
                for (var i = 0; i < elms.length; i++)
                {
                    node.parentNode.insertBefore(elms[i], node);
                }
            }

            return node;
        });
    },
    append: function(html)
    {
        return this._inject(html, function(frag, node)
        {
            if (typeof frag === 'string' || typeof frag === 'number')
            {
                node.insertAdjacentHTML('beforeend', frag);
            }
            else
            {
                var elms = (frag instanceof Node) ? [frag] : this._toArray(frag);
                for (var i = 0; i < elms.length; i++)
                {
                    node.appendChild(elms[i]);
                }
            }

            return node;
        });
    },
    prepend: function(html)
    {
        return this._inject(html, function(frag, node)
        {
            if (typeof frag === 'string' || typeof frag === 'number')
            {
                node.insertAdjacentHTML('afterbegin', frag);
            }
            else
            {
                var elms = (frag instanceof Node) ? [frag] : this._toArray(frag).reverse();
                for (var i = 0; i < elms.length; i++)
                {
                    node.insertBefore(elms[i], node.firstChild);
                }
            }

            return node;
        });
    },
    wrap: function(html)
    {
        return this._inject(html, function(frag, node)
        {
            var wrapper = (typeof frag === 'string' || typeof frag === 'number') ? this.create(frag)[0] : (frag instanceof Node) ? frag : this._toArray(frag)[0];

            if (node.parentNode)
            {
                node.parentNode.insertBefore(wrapper, node);
            }

            wrapper.appendChild(node);

            return new Dom(wrapper);

        });
    },
    unwrap: function()
    {
        return this.each(function(node)
        {
            var $node = new Dom(node);

            return $node.replaceWith($node.contents());
        });
    },
    replaceWith: function(html)
    {
        return this._inject(html, function(frag, node)
        {
            var docFrag = document.createDocumentFragment();
            var elms = (typeof frag === 'string' || typeof frag === 'number') ? this.create(frag) : (frag instanceof Node) ? [frag] : this._toArray(frag);

            for (var i = 0; i < elms.length; i++)
            {
                docFrag.appendChild(elms[i]);
            }

            var result = docFrag.childNodes[0];
            node.parentNode.replaceChild(docFrag, node);

            return result;

        });
    },
    remove: function()
    {
        return this.each(function(node)
        {
            if (node.parentNode) node.parentNode.removeChild(node);
        });
    },
    clone: function(events)
    {
        var nodes = [];
        this.each(function(node)
        {
            var copy = this._clone(node);
            if (events) copy = this._cloneEvents(node, copy);
            nodes.push(copy);
        });

        return new Dom(nodes);
    },

    // show/hide
    show: function()
    {
        return this.each(function(node)
        {
            if (!node.style || !this._hasDisplayNone(node)) return;

            var target = node.getAttribute('domTargetShow');
            var isHidden = (node.classList) ? node.classList.contains(DomHClass) : false;
            var isHiddenMobile = (node.classList) ? node.classList.contains(DomHMClass) : false;
            var type;

            if (isHidden)
            {
                type = DomHClass;
                node.classList.remove(DomHClass);
            }
            else if (isHiddenMobile)
            {
                type = DomHMClass;
                node.classList.remove(DomHMClass);
            }
            else
            {
                node.style.display = (target) ? target : 'block';
            }

            if (type) node.setAttribute('domTargetHide', type);
            node.removeAttribute('domTargetShow');

        }.bind(this));
    },
    hide: function()
    {
        return this.each(function(node)
        {
            if (!node.style || this._hasDisplayNone(node)) return;

            var display = node.style.display;
            var target = node.getAttribute('domTargetHide');

            if (target === DomHClass)
            {
                node.classList.add(DomHClass);
            }
            else if (target === DomHMClass)
            {
                node.classList.add(DomHMClass);
            }
            else
            {
                if (display !== 'block') node.setAttribute('domTargetShow', display);
                node.style.display = 'none';
            }

            node.removeAttribute('domTargetHide');

        });
    },

    // dimensions
    scrollTop: function(value)
    {
        var node = this.get();
        var isWindow = (node === window);
        var isDocument = (node.nodeType === 9);
        var el = (isDocument) ? (document.scrollingElement || document.body.parentNode || document.body || document.documentElement) : node;

        if (value !== undefined)
        {
            if (isWindow) window.scrollTo(0, value);
            else el.scrollTop = value;
            return;
        }

        if (isDocument)
        {
            return (typeof window.pageYOffset != 'undefined') ? window.pageYOffset : ((document.documentElement.scrollTop) ? document.documentElement.scrollTop : ((document.body.scrollTop) ? document.body.scrollTop : 0));
        }
        else
        {
            return (isWindow) ? window.pageYOffset : el.scrollTop;
        }
    },
    offset: function()
    {
        return this._getDim('Offset');
    },
    position: function()
    {
        return this._getDim('Position');
    },
    width: function(value, adjust)
    {
        return this._getSize('width', 'Width', value, adjust);
    },
    height: function(value, adjust)
    {
        return this._getSize('height', 'Height', value, adjust);
    },
    outerWidth: function()
    {
        return this._getInnerOrOuter('width', 'outer');
    },
    outerHeight: function()
    {
        return this._getInnerOrOuter('height', 'outer');
    },
    innerWidth: function()
    {
        return this._getInnerOrOuter('width', 'inner');
    },
    innerHeight: function()
    {
        return this._getInnerOrOuter('height', 'inner');
    },

    // events
    click: function()
    {
        return this._triggerEvent('click');
    },
    focus: function()
    {
        return this._triggerEvent('focus');
    },
    trigger: function(names)
    {
        return this.each(function(node)
        {
            var events = names.split(' ');
            for (var i = 0; i < events.length; i++)
            {
                var ev;
                var opts = { bubbles: true, cancelable: true };

                try {
                    ev = new window.CustomEvent(events[i], opts);
                } catch(e) {
                    ev = document.createEvent('CustomEvent');
                    ev.initCustomEvent(events[i], true, true);
                }

                node.dispatchEvent(ev);
            }
        });
    },
    on: function(names, handler, one)
    {
        return this.each(function(node)
        {
            var events = names.split(' ');
            for (var i = 0; i < events.length; i++)
            {
                var event = this._getEventName(events[i]);
                var namespace = this._getEventNamespace(events[i]);

                handler = (one) ? this._getOneHandler(handler, names) : handler;
                node.addEventListener(event, handler);

                node._e = node._e || {};
                node._e[namespace] = node._e[namespace] || {};
                node._e[namespace][event] = node._e[namespace][event] || [];
                node._e[namespace][event].push(handler);
            }

        });
    },
    one: function(events, handler)
    {
        return this.on(events, handler, true);
    },
    off: function(names, handler)
    {
        var testEvent = function(name, key, event) { return (name === event); };
        var testNamespace = function(name, key, event, namespace) { return (key === namespace); };
        var testEventNamespace = function(name, key, event, namespace) { return (name === event && key === namespace); };
        var testPositive = function() { return true; };

        if (names === undefined)
        {
            // ALL
            return this.each(function(node)
            {
                this._offEvent(node, false, false, handler, testPositive);
            });
        }

        return this.each(function(node)
        {
            var events = names.split(' ');

            for (var i = 0; i < events.length; i++)
            {
                var event = this._getEventName(events[i]);
                var namespace = this._getEventNamespace(events[i]);

                // 1) event without namespace
                if (namespace === '_events') this._offEvent(node, event, namespace, handler, testEvent);
                // 2) only namespace
                else if (!event && namespace !== '_events') this._offEvent(node, event, namespace, handler, testNamespace);
                // 3) event + namespace
                else this._offEvent(node, event, namespace, handler, testEventNamespace);
            }
        });
    },

    // form
    serialize: function(asObject)
    {
        var obj = {};
        var elms = this.get().elements;
        for (var i = 0; i < elms.length; i++)
        {
            var el = elms[i];
            if (/(checkbox|radio)/.test(el.type) && !el.checked) continue;
            if (!el.name || el.disabled || el.type === 'file') continue;

            if (el.type === 'select-multiple')
            {
                for (var z = 0; z < el.options.length; z++)
                {
                    var opt = el.options[z];
                    if (opt.selected) obj[el.name] = opt.value;
                }
            }

            obj[el.name] = (this._isNumber(el.value)) ? parseFloat(el.value) : this._getBooleanFromStr(el.value);
        }

        return (asObject) ? obj : this._toParams(obj);
    },
    ajax: function(success, error)
    {
        if (typeof AjaxRequest !== 'undefined')
        {
            var method = this.attr('method') || 'post';
            var options = {
                url: this.attr('action'),
                data: this.serialize(),
                success: success,
                error: error
            };

            return new AjaxRequest(method, options);
        }
    },

    // private
    _queryContext: function(selector, context)
    {
        context = this._getContext(context);

        return (context.nodeType !== 3 && typeof context.querySelectorAll === 'function') ? context.querySelectorAll(selector) : [];
    },
    _query: function(selector, context)
    {
        if (context)
        {
            return this._queryContext(selector, context);
        }
        else if (/^[.#]?[\w-]*$/.test(selector))
        {
            if (selector[0] === '#')
            {
                var element = document.getElementById(selector.slice(1));
                return element ? [element] : [];
            }

            if (selector[0] === '.')
            {
                return document.getElementsByClassName(selector.slice(1));
            }

            return document.getElementsByTagName(selector);
        }

        return document.querySelectorAll(selector);
    },
    _getContext: function(context)
    {
        context = (typeof context === 'string') ? document.querySelector(context) : context;

        return (context && context.dom) ? context.get() : (context || document);
    },
    _inject: function(html, fn)
    {
        var len = this.nodes.length;
        var nodes = [];
        while (len--)
        {
            var res = (typeof html === 'function') ? html.call(this, this.nodes[len]) : html;
            var el = (len === 0) ? res : this._clone(res);
            var node = fn.call(this, el, this.nodes[len]);

            if (node)
            {
                if (node.dom) nodes.push(node.get());
                else nodes.push(node);
            }
        }

        return new Dom(nodes);
    },
    _cloneEvents: function(node, copy)
    {
        var events = node._e;
        if (events)
        {
            copy._e = events;
            for (var name in events._events)
            {
                for (var i = 0; i < events._events[name].length; i++)
                {
                    copy.addEventListener(name, events._events[name][i]);
                }
            }
        }

        return copy;
    },
    _clone: function(node)
    {
        if (typeof node === 'undefined') return;
        if (typeof node === 'string') return node;
        else if (node instanceof Node || node.nodeType) return node.cloneNode(true);
        else if ('length' in node)
        {
            return [].map.call(this._toArray(node), function(el) { return el.cloneNode(true); });
        }
    },
    _slice: function(obj)
    {
        return (!obj || obj.length === 0) ? [] : (obj.length) ? [].slice.call(obj.nodes || obj) : [obj];
    },
    _eachClass: function(value, type)
    {
        return this.each(function(node)
        {
            if (value)
            {
                var setClass = function(name) { if (node.classList) node.classList[type](name); };
                value.split(' ').forEach(setClass);
            }
        });
    },
    _triggerEvent: function(name)
    {
        var node = this.get();
        if (node && node.nodeType !== 3) node[name]();
        return this;
    },
    _getOneHandler: function(handler, events)
    {
        var self = this;
        return function()
        {
            handler.apply(this, arguments);
            self.off(events);
        };
    },
    _getEventNamespace: function(event)
    {
        var arr = event.split('.');
        var namespace = (arr[1]) ? arr[1] : '_events';
        return (arr[2]) ? namespace + arr[2] : namespace;
    },
    _getEventName: function(event)
    {
        return event.split('.')[0];
    },
    _offEvent: function(node, event, namespace, handler, condition)
    {
        for (var key in node._e)
        {
            for (var name in node._e[key])
            {
                if (condition(name, key, event, namespace))
                {
                    var handlers = node._e[key][name];
                    for (var i = 0; i < handlers.length; i++)
                    {
                        if (typeof handler !== 'undefined' && handlers[i].toString() !== handler.toString())
                        {
                            continue;
                        }

                        node.removeEventListener(name, handlers[i]);
                        node._e[key][name].splice(i, 1);

                        if (node._e[key][name].length === 0) delete node._e[key][name];
                        if (Object.keys(node._e[key]).length === 0) delete node._e[key];
                    }
                }
            }
        }
    },
    _getInnerOrOuter: function(method, type)
    {
        return this[method](undefined, type);
    },
    _getDocSize: function(node, type)
    {
        var body = node.body, html = node.documentElement;
        return Math.max(body['scroll' + type], body['offset' + type], html['client' + type], html['scroll' + type], html['offset' + type]);
    },
    _getSize: function(type, captype, value, adjust)
    {
        if (value === undefined)
        {
            var el = this.get();
            if (el.nodeType === 3)      value = 0;
            else if (el.nodeType === 9) value = this._getDocSize(el, captype);
            else if (el === window)     value = window['inner' + captype];
            else                        value = this._getHeightOrWidth(type, el, adjust || 'normal');

            return Math.round(value);
        }

        return this.each(function(node)
        {
            value = parseFloat(value);
            value = value + this._adjustResultHeightOrWidth(type, node, adjust || 'normal');

            new Dom(node).css(type, value + 'px');

        }.bind(this));
    },
    _getHeightOrWidth: function(type, el, adjust)
    {
        if (!el) return 0;

        var name = type.charAt(0).toUpperCase() + type.slice(1);
        var result = 0;
        var style = getComputedStyle(el, null);
        var $el = new Dom(el);
        var $targets = $el.parents().filter(function(node)
        {
            return (node.nodeType === 1 && getComputedStyle(node, null).display === 'none') ? node : false;
        });

        if (style.display === 'none') $targets.add(el);
        if ($targets.length !== 0)
        {
            var fixStyle = 'visibility: hidden !important; display: block !important;';
            var tmp = [];

            $targets.each(function(node)
            {
                var $node = new Dom(node);
                var thisStyle = $node.attr('style');
                if (thisStyle !== null) tmp.push(thisStyle);
                $node.attr('style', (thisStyle !== null) ? thisStyle + ';' + fixStyle : fixStyle);
            });

            result = $el.get()['offset' + name] - this._adjustResultHeightOrWidth(type, el, adjust);

            $targets.each(function(node, i)
            {
                var $node = new Dom(node);
                if (tmp[i] === undefined) $node.removeAttr('style');
                else $node.attr('style', tmp[i]);
            });
        }
        else
        {
            result = el['offset' + name] - this._adjustResultHeightOrWidth(type, el, adjust);
        }

        return result;
    },
    _adjustResultHeightOrWidth: function(type, el, adjust)
    {
        if (!el || adjust === false) return 0;

        var fix = 0;
        var style = getComputedStyle(el, null);
        var isBorderBox = (style.boxSizing === "border-box");

        if (type === 'height')
        {
            if (adjust === 'inner' || (adjust === 'normal' && isBorderBox))
            {
                fix += (parseFloat(style.borderTopWidth) || 0) + (parseFloat(style.borderBottomWidth) || 0);
            }

            if (adjust === 'outer') fix -= (parseFloat(style.marginTop) || 0) + (parseFloat(style.marginBottom) || 0);
        }
        else
        {
            if (adjust === 'inner' || (adjust === 'normal' && isBorderBox))
            {
                fix += (parseFloat(style.borderLeftWidth) || 0) + (parseFloat(style.borderRightWidth) || 0);
            }

            if (adjust === 'outer') fix -= (parseFloat(style.marginLeft) || 0) + (parseFloat(style.marginRight) || 0);
        }

        return fix;
    },
    _getDim: function(type)
    {
        var node = this.get();
        return (node.nodeType === 3) ? { top: 0, left: 0 } : this['_get' + type](node);
    },
    _getPosition: function(node)
    {
        return { top: node.offsetTop, left: node.offsetLeft };
    },
    _getOffset: function(node)
    {
        var rect = node.getBoundingClientRect();
        var doc = node.ownerDocument;
		var docElem = doc.documentElement;
		var win = doc.defaultView;

		return {
			top: rect.top + win.pageYOffset - docElem.clientTop,
			left: rect.left + win.pageXOffset - docElem.clientLeft
		};
    },
    _getSibling: function(selector, method)
    {
        selector = (selector && selector.dom) ? selector.get() : selector;

        var isNode = (selector && selector.nodeType);
        var sibling;

        this.each(function(node)
        {
            while (node = node[method])
            {
                if ((isNode && node === selector) || new Dom(node).is(selector))
                {
                    sibling = node;
                    return;
                }
            }
        });

        return new Dom(sibling);
    },
    _toArray: function(obj)
    {
        if (obj instanceof NodeList)
        {
            var arr = [];
            for (var i = 0; i < obj.length; i++)
            {
                arr[i] = obj[i];
            }

            return arr;
        }
        else if (obj === undefined) return [];
        else
        {
            return (obj.dom) ? obj.nodes : obj;
        }
    },
    _toParams: function(obj)
    {
        var params = '';
        for (var key in obj)
        {
            params += '&' + this._encodeUri(key) + '=' + this._encodeUri(obj[key]);
        }

        return params.replace(/^&/, '');
    },
    _toObject: function(str)
    {
        return (new Function("return " + str))();
    },
    _encodeUri: function(str)
    {
        return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
    },
    _isNumber: function(str)
    {
        return !isNaN(str) && !isNaN(parseFloat(str));
    },
    _isObjectString: function(str)
    {
        return (str.search(/^{/) !== -1);
    },
    _getBooleanFromStr: function(str)
    {
        if (str === 'true') return true;
        else if (str === 'false') return false;

        return str;
    },
    _hasDisplayNone: function(el)
    {
        return (el.style.display === 'none') || ((el.currentStyle) ? el.currentStyle.display : getComputedStyle(el, null).display) === 'none';
    }
};
// Unique ID of Application
var uuid = 0;

// Wrapper
var $RE = function(selector, options)
{
    return MyAppSelector(selector, options, [].slice.call(arguments, 2));
};

// Globals
$RE.app = [];
$RE.version = '1.0.7';
$RE.options = {};
$RE.modules = {};
$RE.services = {};
$RE.plugins = {};
$RE.classes = {};
$RE.extends = {};
$RE.lang = {};
$RE.dom = function(selector, context) { return new Dom(selector, context); };
$RE.ajax = Ajax;
$RE.Dom = Dom;
$RE.env = {
    'module': 'modules',
    'service': 'services',
    'plugin': 'plugins',
    'class': 'classes',
    'extend': 'extends'
};

// selector class
var MyAppSelector = function(selector, options, args)
{
    var namespace = 'revolvapp';
    var nodes = (Array.isArray(selector)) ? selector : (selector && selector.nodeType) ? [selector] : document.querySelectorAll(selector);
    var isApi = (typeof options === 'string' || typeof options === 'function');
    var isDestroy = (options === 'destroy');
    var instance, value = [];

    for (var i = 0; i < nodes.length; i++)
    {
        var el = nodes[i];
        var $el = $RE.dom(el);

        instance = $el.dataget(namespace);
        if (!instance && !isApi)
        {
            // Initialization
            instance = new App(el, options, uuid);
            $el.dataset(namespace, instance);
            $el.attr('data-uuid', uuid);
            $RE.app[uuid] = instance;
            uuid++;
        }

        // API
        if (instance && isApi)
        {
            options = (isDestroy) ? 'stop' : options;

            var methodValue;
            if (typeof options === 'function')
            {
                methodValue = options.apply(instance, args);
            }
            else
            {
                args.unshift(options);
                methodValue = instance.api.apply(instance, args);
            }

            if (methodValue !== undefined) value.push(methodValue);
            if (isDestroy)
            {
                var index = $el.attr('data-uuid');
                $el.dataset(namespace, false);
                $RE.app.splice(index, 1);
            }


        }
    }

    return (value.length === 0 || value.length === 1) ? ((value.length === 0) ? instance : value[0]) : value;
};

// add
$RE.add = function(type, name, obj)
{
    if (typeof $RE.env[type] === 'undefined') return;

    // translations
    if (obj.translations)
    {
        $RE.lang = $RE.extend(true, {}, $RE.lang, obj.translations);
    }

    // extend
    if (type === 'extend')
    {
        $RE[$RE.env[type]][name] = obj;
    }
    else
    {
        // prototype
        var F = function() {};
        F.prototype = obj;

        // extends
        if (obj.extends)
        {
            for (var i = 0; i < obj.extends.length; i++)
            {
                $RE.inherit(F, $RE.extends[obj.extends[i]]);
            }
        }

        $RE[$RE.env[type]][name] = F;
    }
};

// add lang
$RE.addLang = function(lang, obj)
{
    if (typeof $RE.lang[lang] === 'undefined')
    {
        $RE.lang[lang] = {};
    }

    $RE.lang[lang] = $RE.extend($RE.lang[lang], obj);
};

// create
$RE.create = function(name)
{
    var arr = name.split('.');
    var args = [].slice.call(arguments, 1);

    var type = 'classes';
    if (typeof $RE.env[arr[0]] !== 'undefined')
    {
        type = $RE.env[arr[0]];
        name = arr.slice(1).join('.');
    }

    // construct
    var instance = new $RE[type][name]();

    instance._type = arr[0];
    instance._name = name;

    // init
    if (instance.init)
    {
        var res = instance.init.apply(instance, args);

        return (res) ? res : instance;
    }

    return instance;
};

// inherit
$RE.inherit = function(current, parent)
{
    var F = function () {};
    F.prototype = parent;
    var f = new F();

    for (var prop in current.prototype)
    {
        if (current.prototype.__lookupGetter__(prop)) f.__defineGetter__(prop, current.prototype.__lookupGetter__(prop));
        else f[prop] = current.prototype[prop];
    }

    current.prototype = f;
    current.prototype.super = parent;

    return current;
};

// error
$RE.error = function(exception)
{
    throw exception;
};

// extend
$RE.extend = function()
{
    var extended = {};
    var deep = false;
    var i = 0;
    var length = arguments.length;

    if (Object.prototype.toString.call( arguments[0] ) === '[object Boolean]')
    {
        deep = arguments[0];
        i++;
    }

    var merge = function(obj)
    {
        for (var prop in obj)
        {
            if (Object.prototype.hasOwnProperty.call(obj, prop))
            {
                if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') extended[prop] = $RE.extend(true, extended[prop], obj[prop]);
                else extended[prop] = obj[prop];
            }
        }
    };

    for (; i < length; i++ )
    {
        var obj = arguments[i];
        merge(obj);
    }

    return extended;
};
$RE.opts = {
    animation: true,
    lang: 'en',
    path: '',
    template: '',
    images: false,
    autosave: false,
    upload: false,
    edit: true,
    code: true,
    baseColors: ['#ffffff', '#111113'],
    doctype: '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
    buttons: ['bold', 'italic', 'strikethrough', 'link'],

    // frame
    frame: {
        'width': '100%',
        'border': 'none',
        'background-color': '#fff',
        'margin-left': 'auto',
        'margin-right': 'auto'
    },

    // align
    align: 'left', // center, right

    // width
    width: '600px',
    widthPreview: {
        desktop: 600,
        mobile: 320
    },

    // styles
    styles: {
        'text': {
            'font-family': 'Helvetica, Arial, sans-serif',
            'font-size': '14px',
            'line-height': '21px',
            'color': '#111113'
        },
        'link': {
            'color': '#2c76ee'
        },
        'button-link': {
            'font-size': '18px',
        },
        'button': {
            'font-size': '15px',
            'font-weight': 'bold',
            'color': '#ffffff',
            'background-color': '#2c76ee'
        }
    },

    // text
    textLineHeight: 1.5,

    // headings
    headingsLineHeight: 1.2,
    headings: {
        'h1': '36px',
        'h2': '24px',
        'h3': '18px'
    },

    // card
    card: {
        duplicate: true,
        settings: true,
        spacer: false // or px height
    },

    // block
    block: {
        add: true,
        sort: true,
        duplicate: true,
        settings: true,
        trash: true
    },
    blocks: {
        'Headings': {
            'heading-1': '<re-block><re-heading type="h1">Heading</re-heading></re-block>',
            'heading-2': '<re-block><re-heading type="h2">Heading</re-heading></re-block>',
            'heading-3': '<re-block><re-heading type="h3">Heading</re-heading></re-block>'
        },
        'Text': {
            'text-1': '<re-block><re-text>Type some text ...</re-text></re-block>',
            'text-2': '<re-block><re-grid><re-row><re-column width="300px" padding="0 0 20px 0"><re-text>Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="300px" padding="0 0 20px 0"><re-text>Type some text ...</re-text></re-column></re-row></re-grid></re-block>',
            'text-3': '<re-block><re-grid><re-row><re-column width="200px" padding="0 0 20px 0"><re-text>Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="200px" padding="0 0 20px 0"><re-text>Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="200px" padding="0 0 20px 0"><re-text>Type some text ...</re-text></re-column></re-row></re-grid></re-block>'
        },
        'Images': {
            'image-1': '<re-block><re-image src="{{--image-placeholder--}}"></re-image></re-block>',
            'image-2': '<re-block><re-grid><re-row><re-column width="300px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="300px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image></re-column></re-row></re-grid></re-block>',
            'image-3': '<re-block><re-grid><re-row><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image></re-column></re-row></re-grid></re-block>',
            'image-1-text-right': '<re-block><re-grid><re-row><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="400px" padding="0 0 20px 0"><re-text>Type some text ...</re-text></re-column></re-row></re-grid></re-block>',
            'image-2-text': '<re-block><re-grid><re-row><re-column width="300px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image><re-text padding="4px 0 0 0">Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="300px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image><re-text padding="4px 0 0 0">Type some text ...</re-text></re-column></re-row></re-grid></re-block>',
            'image-3-text': '<re-block><re-grid><re-row><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image><re-text padding="4px 0 0 0">Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image><re-text padding="4px 0 0 0">Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image><re-text padding="4px 0 0 0">Type some text ...</re-text></re-column></re-row></re-grid></re-block>',
            'image-1-text-left': '<re-block><re-grid><re-row><re-column width="400px" padding="0 0 20px 0"><re-text>Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image></re-column></re-row></re-grid></re-block>',
            'image-2-heading-text': '<re-block><re-grid><re-row><re-column width="300px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image><re-heading type="h2" padding="4px 0">Heading</re-heading><re-text>Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="300px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image><re-heading type="h2" padding="4px 0">Heading</re-heading><re-text>Type some text ...</re-text></re-column></re-row></re-grid></re-block>',
            'image-3-heading-text': '<re-block><re-grid><re-row><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image><re-heading type="h3" padding="4px 0">Heading</re-heading><re-text>Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image><re-heading type="h3" padding="4px 0">Heading</re-heading><re-text>Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image><re-heading type="h3" padding="4px 0">Heading</re-heading><re-text>Type some text ...</re-text></re-column></re-row></re-grid></re-block>',
            'image-1-heading-text-left': '<re-block><re-grid><re-row><re-column width="400px" padding="0 0 20px 0"><re-heading type="h2" padding="4px 0">Heading</re-heading><re-text>Type some text ...</re-text></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image></re-column></re-row></re-grid></re-block>',
            'image-1-heading-text-right': '<re-block><re-grid><re-row><re-column width="200px" padding="0 0 20px 0"><re-image src="{{--image-placeholder--}}"></re-image></re-column><re-column-spacer width="16px"></re-column-spacer><re-column width="400px" padding="0 0 20px 0"><re-heading type="h2" padding="4px 0">Heading</re-heading><re-text>Type some text ...</re-text></re-column></re-row></re-grid></re-block>'
        },
        'Button': {
            'button': '<re-block><re-button href="http://example.com/">Button</re-button></re-block>',
            'button-link': '<re-block><re-button-link href="http://example.com/">Link</re-button-link></re-block>',
            'button-app': '<re-block><re-button-app width="130px" src="{{--app-store-placeholder--}}" href="http://example.com/">App Store</re-button-app><re-inline-spacer padding="0 4px"></re-inline-spacer><re-button-app width="130px" src="{{--google-play-placeholder--}}" href="http://example.com/">Google Play</re-button-app></re-block>'
        }
    },

    // misc
    stylePairs: {
        "background-color": "bgcolor",
        "text-align": "align"
    },

    // icons
    icons: {
        "settings": '<svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M11,15 C8.790861,15 7,13.209139 7,11 C7,8.790861 8.790861,7 11,7 C13.209139,7 15,8.790861 15,11 C15,13.209139 13.209139,15 11,15 Z M11,13 C12.1045695,13 13,12.1045695 13,11 C13,9.8954305 12.1045695,9 11,9 C9.8954305,9 9,9.8954305 9,11 C9,12.1045695 9.8954305,13 11,13 Z"></path><path d="M19.91,12 L20,12 C20.5522847,12 21,11.5522847 21,11 C21,10.4477153 20.5522847,10 20,10 L19.8260117,9.99999205 C18.7697973,9.9957795 17.8169395,9.36474787 17.4008602,8.39393144 C17.3646445,8.30943133 17.3403365,8.22053528 17.3284579,8.12978593 C16.997162,7.1847178 17.2270428,6.12462982 17.9428932,5.39289322 L18.0032867,5.3325 C18.1910637,5.14493174 18.2965733,4.89040925 18.2965733,4.625 C18.2965733,4.35959075 18.1910637,4.10506826 18.0025,3.91671334 C17.8149317,3.7289363 17.5604092,3.62342669 17.295,3.62342669 C17.0295908,3.62342669 16.7750683,3.7289363 16.5871068,3.91710678 L16.519265,3.98486258 C15.7448438,4.74238657 14.5873187,4.9522675 13.6060686,4.51913983 C12.6352521,4.10306046 12.0042205,3.1502027 12,2.09 L12,2 C12,1.44771525 11.5522847,1 11,1 C10.4477153,1 10,1.44771525 10,2 L9.99999205,2.17398831 C9.9957795,3.2302027 9.36474787,4.18306046 8.39393144,4.59913983 C8.30943133,4.63535548 8.22053528,4.65966354 8.12978593,4.67154209 C7.1847178,5.00283804 6.12462982,4.77295717 5.39289322,4.05710678 L5.3325,3.99671334 C5.14493174,3.8089363 4.89040925,3.70342669 4.625,3.70342669 C4.35959075,3.70342669 4.10506826,3.8089363 3.91671334,3.9975 C3.7289363,4.18506826 3.62342669,4.43959075 3.62342669,4.705 C3.62342669,4.97040925 3.7289363,5.22493174 3.91710678,5.41289322 L3.98486258,5.48073504 C4.74238657,6.25515616 4.9522675,7.41268129 4.5385361,8.34518109 C4.16293446,9.36642969 3.2012163,10.0542811 2.09,10.08 L2,10.08 C1.44771525,10.08 1,10.5277153 1,11.08 C1,11.6322847 1.44771525,12.08 2,12.08 L2.17398831,12.080008 C3.2302027,12.0842205 4.18306046,12.7152521 4.59486258,13.6762347 C5.0322675,14.6673187 4.82238657,15.8248438 4.05710678,16.6071068 L3.99671334,16.6675 C3.8089363,16.8550683 3.70342669,17.1095908 3.70342669,17.375 C3.70342669,17.6404092 3.8089363,17.8949317 3.9975,18.0832867 C4.18506826,18.2710637 4.43959075,18.3765733 4.705,18.3765733 C4.97040925,18.3765733 5.22493174,18.2710637 5.41289322,18.0828932 L5.48073504,18.0151374 C6.25515616,17.2576134 7.41268129,17.0477325 8.34518109,17.4614639 C9.36642969,17.8370655 10.0542811,18.7987837 10.08,19.91 L10.08,20 C10.08,20.5522847 10.5277153,21 11.08,21 C11.6322847,21 12.08,20.5522847 12.08,20 L12.080008,19.8260117 C12.0842205,18.7697973 12.7152521,17.8169395 13.6762347,17.4051374 C14.6673187,16.9677325 15.8248438,17.1776134 16.6071068,17.9428932 L16.6675,18.0032867 C16.8550683,18.1910637 17.1095908,18.2965733 17.375,18.2965733 C17.6404092,18.2965733 17.8949317,18.1910637 18.0832867,18.0025 C18.2710637,17.8149317 18.3765733,17.5604092 18.3765733,17.295 C18.3765733,17.0295908 18.2710637,16.7750683 18.0828932,16.5871068 L18.0151374,16.519265 C17.2588636,15.7461219 17.0484268,14.5911384 17.482977,13.6011431 C17.9001657,12.6331176 18.8515919,12.0042134 19.91,12 Z M17.4851374,13.5962347 L17.4808602,13.6060686 C17.4815645,13.6044252 17.4822703,13.6027829 17.4829777,13.6011415 C17.4836954,13.5995065 17.4844155,13.5978703 17.4851374,13.5962347 Z M19.2987985,7.71517468 C19.4176633,7.89040605 19.6163373,7.99914118 19.83,8 L20,8 C21.6568542,8 23,9.34314575 23,11 C23,12.6568542 21.6568542,14 20,14 L19.9139883,13.999992 C19.6549169,14.0010253 19.421197,14.1558067 19.3191398,14.3939314 C19.2075746,14.6468614 19.2590548,14.9307827 19.4371068,15.1128932 L19.4967133,15.1725 C20.0600445,15.7352048 20.3765733,16.4987723 20.3765733,17.295 C20.3765733,18.0912277 20.0600445,18.8547952 19.4975,19.4167133 C18.9347952,19.9800445 18.1712277,20.2965733 17.375,20.2965733 C16.5787723,20.2965733 15.8152048,19.9800445 15.2528932,19.4171068 L15.200735,19.3648626 C15.0107827,19.1790548 14.7268614,19.1275746 14.4739314,19.2391398 C14.2358067,19.341197 14.0810253,19.5749169 14.08,19.83 L14.08,20 C14.08,21.6568542 12.7368542,23 11.08,23 C9.42314575,23 8.08,21.6568542 8.08,20 C8.07403212,19.6665579 7.90531385,19.4306648 7.59623466,19.3148626 C7.35313857,19.2075746 7.06921731,19.2590548 6.88710678,19.4371068 L6.8275,19.4967133 C6.26479523,20.0600445 5.50122774,20.3765733 4.705,20.3765733 C3.90877226,20.3765733 3.14520477,20.0600445 2.58328666,19.4975 C2.01995553,18.9347952 1.70342669,18.1712277 1.70342669,17.375 C1.70342669,16.5787723 2.01995553,15.8152048 2.58289322,15.2528932 L2.63513742,15.200735 C2.82094519,15.0107827 2.87242541,14.7268614 2.76086017,14.4739314 C2.65880297,14.2358067 2.42508314,14.0810253 2.17,14.08 L2,14.08 C0.343145751,14.08 -1,12.7368542 -1,11.08 C-1,9.42314575 0.343145751,8.08 2,8.08 C2.33344206,8.07403212 2.56933519,7.90531385 2.68513742,7.59623466 C2.79242541,7.35313857 2.74094519,7.06921731 2.56289322,6.88710678 L2.50328666,6.8275 C1.93995553,6.26479523 1.62342669,5.50122774 1.62342669,4.705 C1.62342669,3.90877226 1.93995553,3.14520477 2.5025,2.58328666 C3.06520477,2.01995553 3.82877226,1.70342669 4.625,1.70342669 C5.42122774,1.70342669 6.18479523,2.01995553 6.74710678,2.58289322 L6.79926496,2.63513742 C6.98921731,2.82094519 7.27313857,2.87242541 7.51623466,2.76513742 C7.58030647,2.73685997 7.64699387,2.71546911 7.71517468,2.70120146 C7.89040605,2.58233675 7.99914118,2.3836627 8,2.17 L8,2 C8,0.343145751 9.34314575,-1 11,-1 C12.6568542,-1 14,0.343145751 14,2 L13.999992,2.08601169 C14.0010253,2.34508314 14.1558067,2.57880297 14.4037653,2.68513742 C14.6468614,2.79242541 14.9307827,2.74094519 15.1128932,2.56289322 L15.1725,2.50328666 C15.7352048,1.93995553 16.4987723,1.62342669 17.295,1.62342669 C18.0912277,1.62342669 18.8547952,1.93995553 19.4167133,2.5025 C19.9800445,3.06520477 20.2965733,3.82877226 20.2965733,4.625 C20.2965733,5.42122774 19.9800445,6.18479523 19.4171068,6.74710678 L19.3648626,6.79926496 C19.1790548,6.98921731 19.1275746,7.27313857 19.2348626,7.51623466 C19.26314,7.58030647 19.2845309,7.64699387 19.2987985,7.71517468 Z"></path></g></svg>',
        "duplicate": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m11 10c-.5522847 0-1 .4477153-1 1v9c0 .5522847.4477153 1 1 1h9c.5522847 0 1-.4477153 1-1v-9c0-.5522847-.4477153-1-1-1zm0-2h9c1.6568542 0 3 1.34314575 3 3v9c0 1.6568542-1.3431458 3-3 3h-9c-1.65685425 0-3-1.3431458-3-3v-9c0-1.65685425 1.34314575-3 3-3z"/><path d="m5 14c.55228475 0 1 .4477153 1 1s-.44771525 1-1 1h-1c-1.65685425 0-3-1.3431458-3-3v-9c0-1.65685425 1.34314575-3 3-3h9c1.6568542 0 3 1.34314575 3 3v1c0 .55228475-.4477153 1-1 1s-1-.44771525-1-1v-1c0-.55228475-.4477153-1-1-1h-9c-.55228475 0-1 .44771525-1 1v9c0 .5522847.44771525 1 1 1z"/></svg>',
        "trash": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m3 7c-.55228475 0-1-.44771525-1-1s.44771525-1 1-1h18c.5522847 0 1 .44771525 1 1s-.4477153 1-1 1z"/><path d="m18 6c0-.55228475.4477153-1 1-1s1 .44771525 1 1v14c0 1.6568542-1.3431458 3-3 3h-10c-1.65685425 0-3-1.3431458-3-3v-14c0-.55228475.44771525-1 1-1s1 .44771525 1 1v14c0 .5522847.44771525 1 1 1h10c.5522847 0 1-.4477153 1-1zm-9 0c0 .55228475-.44771525 1-1 1s-1-.44771525-1-1v-2c0-1.65685425 1.34314575-3 3-3h4c1.6568542 0 3 1.34314575 3 3v2c0 .55228475-.4477153 1-1 1s-1-.44771525-1-1v-2c0-.55228475-.4477153-1-1-1h-4c-.55228475 0-1 .44771525-1 1z"/><path d="m9 11c0-.5522847.44771525-1 1-1 .5522847 0 1 .4477153 1 1v6c0 .5522847-.4477153 1-1 1-.55228475 0-1-.4477153-1-1z"/><path d="m13 11c0-.5522847.4477153-1 1-1s1 .4477153 1 1v6c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1z"/></svg>',
        "add": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><rect height="2" rx="1" width="16" x="4" y="11"/><rect height="16" rx="1" width="2" x="11" y="4"/></svg>',
        "add-circle": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m12 23c-6.07513225 0-11-4.9248678-11-11 0-6.07513225 4.92486775-11 11-11 6.0751322 0 11 4.92486775 11 11 0 6.0751322-4.9248678 11-11 11zm0-2c4.9705627 0 9-4.0294373 9-9 0-4.97056275-4.0294373-9-9-9-4.97056275 0-9 4.02943725-9 9 0 4.9705627 4.02943725 9 9 9z" fill-rule="nonzero"/><rect height="2" rx="1" width="10" x="7" y="11"/><rect height="10" rx="1" width="2" x="11" y="7"/></svg>',
        "sort": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><g transform="translate(4 9)"><path d="m0 7c-.55228475 0-1-.44771525-1-1s.44771525-1 1-1h16c.5522847 0 1 .44771525 1 1s-.4477153 1-1 1z"/><path d="m0 1c-.55228475 0-1-.44771525-1-1s.44771525-1 1-1h16c.5522847 0 1 .44771525 1 1s-.4477153 1-1 1z"/></g></svg>',
        "desktop": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m4 4c-.55228475 0-1 .44771525-1 1v10c0 .5522847.44771525 1 1 1h16c.5522847 0 1-.4477153 1-1v-10c0-.55228475-.4477153-1-1-1zm0-2h16c1.6568542 0 3 1.34314575 3 3v10c0 1.6568542-1.3431458 3-3 3h-16c-1.65685425 0-3-1.3431458-3-3v-10c0-1.65685425 1.34314575-3 3-3z"/><path d="m8 22c-.55228475 0-1-.4477153-1-1s.44771525-1 1-1h8c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1z"/><path d="m11 17c0-.5522847.4477153-1 1-1s1 .4477153 1 1v4c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1z"/></svg>',
        "mobile": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m7 3c-.55228475 0-1 .44771525-1 1v16c0 .5522847.44771525 1 1 1h10c.5522847 0 1-.4477153 1-1v-16c0-.55228475-.4477153-1-1-1zm0-2h10c1.6568542 0 3 1.34314575 3 3v16c0 1.6568542-1.3431458 3-3 3h-10c-1.65685425 0-3-1.3431458-3-3v-16c0-1.65685425 1.34314575-3 3-3z"/></svg>',
        'code': '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m9.38360299 19.1825515c.3959379-.4117133.38300721-1.0665238-.02870615-1.4627203l-5.94837874-5.7195054 5.94837874-5.71976406c.41171336-.39593791.42464405-1.05074835.02870615-1.46246171-.39593791-.41223059-1.05074836-.42490267-1.46272033-.02870614l-6.72396188 6.46534801c-.20275332.1947362-.31731928.4642119-.31731928.7455839 0 .2813719.11456596.550589.31731928.7455839l6.72396188 6.465348c.2006844.192926.45903971.2888717.71687778.2888717.27154462 0 .54283062-.1065489.74584255-.3175779m5.97837791.3175779c-.2715446 0-.542572-.1062903-.7458425-.3175779-.3959379-.4117133-.3830072-1.0665238.0287061-1.4627203l5.9486374-5.7195054-5.9486374-5.71976406c-.4117133-.39593791-.424644-1.05074835-.0287061-1.46246171.3959379-.41197197 1.0507484-.42464405 1.4627203-.02870614l6.7239619 6.46534801c.2027533.1947362.3175779.4642119.3175779.7455839 0 .2813719-.1148246.550589-.3175779.7455839l-6.7239619 6.465348c-.2006844.192926-.4587811.2888717-.7168778.2888717"/></svg>',
        "align-left": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><g><path d="m17 9c.5522847 0 1 .44771525 1 1 0 .5522847-.4477153 1-1 1h-14c-.55228475 0-1-.4477153-1-1 0-.55228475.44771525-1 1-1z"/><path d="m21 5c.5522847 0 1 .44771525 1 1s-.4477153 1-1 1h-18c-.55228475 0-1-.44771525-1-1s.44771525-1 1-1z"/><path d="m21 13c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1h-18c-.55228475 0-1-.4477153-1-1s.44771525-1 1-1z"/><path d="m17 17c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1h-14c-.55228475 0-1-.4477153-1-1s.44771525-1 1-1z"/></g></svg>',
        "align-center": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><g><path d="m19 9c.5522847 0 1 .44771525 1 1 0 .5522847-.4477153 1-1 1h-14c-.55228475 0-1-.4477153-1-1 0-.55228475.44771525-1 1-1z"/><path d="m21 5c.5522847 0 1 .44771525 1 1s-.4477153 1-1 1h-18c-.55228475 0-1-.44771525-1-1s.44771525-1 1-1z"/><path d="m21 13c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1h-18c-.55228475 0-1-.4477153-1-1s.44771525-1 1-1z"/><path d="m19 17c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1h-14c-.55228475 0-1-.4477153-1-1s.44771525-1 1-1z"/></g></svg>',
        "align-right": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><g><path d="m21 9c.5522847 0 1 .44771525 1 1 0 .5522847-.4477153 1-1 1h-14c-.55228475 0-1-.4477153-1-1 0-.55228475.44771525-1 1-1z"/><path d="m21 5c.5522847 0 1 .44771525 1 1s-.4477153 1-1 1h-18c-.55228475 0-1-.44771525-1-1s.44771525-1 1-1z"/><path d="m21 13c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1h-18c-.55228475 0-1-.4477153-1-1s.44771525-1 1-1z"/><path d="m21 17c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1h-14c-.55228475 0-1-.4477153-1-1s.44771525-1 1-1z"/></g></svg>',
        "bold": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m16.7562177 20.376934c1.2847165-1.0078564 2.0155706-2.6063262 1.9572789-4.2808152.1730396-2.4065537-1.2998837-4.6088352-3.5266287-5.2729354 1.4129538-.6368181 2.3420534-2.07098361 2.3804744-3.67451947.0507787-1.43215308-.566842-2.80134657-1.6575155-3.67451946-1.3408531-1.01031914-2.96608-1.52738221-4.6198837-1.46980779-1.0227223 0-2.7860367.061242-5.2899431.18372598v19.80565984h5.7131386c1.8084469.0732866 3.5869854-.4969049 5.0430791-1.6167885zm-5.5544403-15.35949136c1.9514012 0 2.9271018.75940069 2.9271018 2.27820207 0 1.71477574-1.0932549 2.57216362-3.2797647 2.57216362-.5642606 0-1.10501032 0-1.6222492 0v-4.7768753h1.9220127zm-.2292309 7.47764706c1.1496112-.1096164 2.3031888.1603411 3.2973979.7716491.6657628.6140384 1.0125276 1.5207133.9345566 2.4435555.0812758 1.0125992-.32098 2.0020082-1.0756218 2.645654-.9817099.6113234-2.1222928.8875493-3.2621315.7900217-.4231955 0-.97570066-.030621-1.65751555-.091863v-6.5222721h1.76331435z"/></svg>',
        "italic": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m11.3974256 22 3.0571199-20h-2.3974256l-3.0571199 20z"/></svg>',
        "strikethrough": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m22.6680253 12.8715919h-6.8976547c-.6595712-.6970197-1.451428-1.2556163-2.3293128-1.6431586l-1.6070452-.7583809c-.7136989-.3330752-1.3913599-.73846168-2.02234899-1.20979803-.33268887-.27329076-.59830362-.61920765-.77643757-1.01117451-.18734794-.42645793-.2797646-.88854121-.27085032-1.35425158-.02514903-.73531491.25603429-1.44808194.77643757-1.96817896.58181431-.50761484 1.34261211-.76121413 2.11263251-.70421083.6759446.00979838 1.3461689.1255644 1.9862356.34307707.5688898.16716047 1.1102935.41657117 1.6070452.7403242l.7764376-2.29319934c-1.3319093-.79102089-2.8828513-1.13286117-4.4238885-.97506114-1.447841-.05542153-2.86015069.45637876-3.93635793 1.42647833-1.01225244.92343103-1.57250864 2.2416809-1.53481846 3.61133754-.02805887 1.07174232.24735785 2.1295929.79449426 3.05158025.70165561.9522033 1.65989472 1.6849744 2.76267322 2.1126324l1.33619491.6319841h-10.0214617v1.8056688h12.7660782l.1083401.1263968c.3419709.5401789.5235585 1.1663432.523644 1.8056688.0190883.8196445-.3245027 1.6059394-.9389478 2.1487458-.6942518.586318-1.5840242.8893564-2.4918229.8486643-.6770281-.0144061-1.34746633-.136304-1.98623563-.3611337-.64468282-.2064294-1.25371556-.5109458-1.80566877-.9028344l-.95700445 2.4376528c1.31403692.8360174 2.84912407 1.2575372 4.40583185 1.2097981 1.6983919.0905108 3.3703452-.4496587 4.6947388-1.5167618 1.1355774-.8933917 1.800629-2.2567474 1.8056687-3.7016209-.0338912-.7149124-.1738551-1.4208171-.4153038-2.0945758h5.958707z"/></svg>',
        "link": '<svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m16.4734033 18.5273359c-1.1386485-.0058243-2.2586655-.2894967-3.2628079-.826388v-2.0232258h1.3535666c.6026623.2614328 1.2523198.3971821 1.9092413.3989459 2.014488.2012841 3.822255-1.2398728 4.0749478-3.2485598-.2456446-2.0144274-2.0555589-3.46362448-4.0749478-3.26280787-.6579135.00625782-1.3076121.14686424-1.9092413.41319401h-1.3535666v-2.02322584c1.0041424-.5368913 2.1241594-.82056374 3.2628079-.82638802 3.3676387-.21181985 6.275324 2.33320015 6.5113676 5.69922772-.2360436 3.3660276-3.1437289 5.9110476-6.5113676 5.6992277zm-8.82915057.1163108c1.16188616-.0059431 2.30476071-.2954048 3.32939577-.8432531v-2.0645161h-1.38119037c-.61496153.2667681-1.27787735.4052879-1.9482054.4070876-2.05559999.205392-3.90026019-1.2651763-4.15811002-3.3148569.25065776-2.0555381 2.09750908-3.53431069 4.15811002-3.32939579.67134024.00638554 1.33429804.14986148 1.9482054.42162655h1.38119037v-2.06451617c-1.02463506-.54784826-2.16750961-.83730994-3.32939577-.84325308-3.43636604-.2161427-6.40339191 2.38081648-6.64425273 5.81553849.24086082 3.434722 3.20788669 6.0316812 6.64425273 5.8155385zm.6792549-7.2403454h6.51136767v2.8496138h-6.51136767z"/></svg>'
    },

    // template
    templateStyles: '#outlook a{padding:0}.ExternalClass{width:100%}.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:100%}body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{mso-table-lspace:0;mso-table-rspace:0}img{-ms-interpolation-mode:bicubic}img{border:0;outline:none;text-decoration:none}a img{border:none}td img{vertical-align:top}table,table td{border-collapse:collapse}body{margin:0;padding:0;width:100% !important}.mobile-spacer{width:0;display:none}@media all and (max-width:639px){.container{width:100% !important;max-width:600px !important}.mobile{width:auto !important;max-width:100% !important;display:block !important}.mobile-center{text-align:center !important}.mobile-right{text-align:right !important}.mobile-left{text-align:left!important;}.mobile-hidden{max-height:0;display:none !important;mso-hide:all;overflow:hidden}.mobile-spacer{width:auto !important;display:table !important}.mobile-image img {height: auto !important; max-width: 600px !important; width: 100% !important}}',
    templateMsoStyles: '<!--[if mso]><style type="text/css">body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }</style><![endif]-->',
    templateTags: [
        'head',
        'title',
        'font',
        'style',
        'body',
        'container',
        'main',
        'header',
        'card',
        'block',
        'spacer',
        'divider',
        'grid',
        'row',
        'column',
        'column-spacer',
        'footer',
        'text',
        'heading',
        'button',
        'button-link',
        'button-app',
        'image',
        'link',
        'social-link',
        'menu-link',
        'mobile-spacer',
        'preheader',
        'inline-spacer'
    ],
    templateNested: [
        'head',
        'body',
        'container',
        'main',
        'header',
        'card',
        'block',
        'grid',
        'row',
        'column',
        'footer'
    ]
};
$RE.lang['en'] = {

};
var App = function(element, options, uuid)
{
    this.modules = {};
    this.services = [];
    this.queueStart = { 'service': {}, 'module': {} };
    this.queueStop = { 'service': {}, 'module': {} };
    this.started = false;
    this.stopped = false;

    // environment
    this.uuid = uuid;
    this.rootOpts = options;
    this.rootElement = element;
    this.$win = $RE.dom(window);
    this.$doc = $RE.dom(document);
    this.$body = $RE.dom('body');

    // core services
    this.coreServices = ['options', 'lang'];
    this.bindableServices = ['opts', 'lang', '$win', '$doc', '$body']

    this.opts = $RE.create('service.options', this, element, options);
    this.lang = $RE.create('service.lang', this);

    this.appcallback = new App.Callback(this);
    this.appstarter = new App.Starter(this);
    this.appbuilder = new App.Builder(this);
    this.appbroadcast = new App.Broadcast(this);
    this.appapi = new App.Api(this);

    this.build();
    this.start();
};

App.prototype = {

    // build
    build: function()
    {
        this.appbuilder.build();
    },

    // start & stop
    start: function()
    {
        // start
        this.stopped = false;
        this.broadcast('start', this);

        // starter
        this.appstarter.start();

        // started
        this.broadcast('started', this);
        this.started = true;
    },
    stop: function()
    {
        this.started = false;
        this.stopped = true;

        // stop
        this.broadcast('stop', this);

        // stopper
        this.appstarter.stop();

        // stopped
        this.broadcast('stopped', this);
    },

    // starter & stopper
    starter: function(instance, priority)
    {
        var type = (instance._type !== 'service') ? 'module' : instance._type;
        this.queueStart[type][priority] = instance._name;
    },
    stopper: function(instance, priority)
    {
        var type = (instance._type !== 'service') ? 'module' : instance._type;
        this.queueStop[type][priority] = instance._name;
    },

    // started & stopped
    isStarted: function()
    {
        return this.started;
    },
    isStopped: function()
    {
        return this.stopped;
    },

    // broadcast
    broadcast: function(name, sender)
    {
        this.appbroadcast.trigger(name, sender, [].slice.call(arguments, 2));
    },

    // callback
    on: function(name, func)
    {
        this.appcallback.add(name, func);
    },
    off: function(name, func)
    {
        this.appcallback.remove(name, func);
    },

    // api
    getHtml: function(tidy)
    {
        return this.frame.getHtml(tidy);
    },
    getSource: function(tidy)
    {
        return this.frame.getSource(tidy);
    },
    getTemplate: function(tidy)
    {
        return this.frame.getTemplate(tidy);
    },
    setTemplate: function(code)
    {
        this.frame.setTemplate(code);
    },
    api: function(name)
    {
        return this.appapi.trigger(name, [].slice.call(arguments, 1));
    }
};
App.Api = function(app)
{
    this.app = app;
    this.modules = app.modules;
};

App.Api.prototype = {
    trigger: function(name, args)
    {
        var arr = name.split('.');
        var isNamed = (arr.length === 3);
        var isApp = (arr.length === 1);
        var isCallback = (arr[0] === 'on' || arr[0] === 'off');

        var module = arr[0];
        var method = arr[1];
        var id = false;

        if (isApp)
        {
            module = false;
            method = arr[0];
        }
        else if (isNamed)
        {
            method = arr[2];
            id = arr[1];
        }

        // app
        if (isApp)
        {
            if (typeof this.app[method] === 'function')
            {
                return this._call(this.app, method, args);
            }
        }
        // callback
        else if (isCallback)
        {
            return (module === 'on') ? this.app.on(module, args[0]) : this.app.off(module, args[0] || undefined);
        }
        else
        {
            // service
            if (this._isInstanceExists(this.app, module))
            {
                return this._call(this.app[module], method, args);
            }
            // module / plugin / addon
            else if (this._isInstanceExists(this.modules, module))
            {
                return this._call(this.modules[module], method, args);
            }
        }
    },

    // private
    _isInstanceExists: function(obj, name)
    {
        return (typeof obj[name] !== 'undefined');
    },
    _call: function(instance, method, args)
    {
        if (typeof instance[method] === 'function')
        {
            return instance[method].apply(instance, args);
        }
    }
};
App.Broadcast = function(app)
{
    this.app = app;
    this.modules = app.modules;
    this.callback = app.appcallback;
};

App.Broadcast.prototype = {
    trigger: function(name, sender, args)
    {
        if (Array.isArray(name))
        {
            sender._id = name[0];
            name = name[1];
        }

        args.unshift(sender);

        for (var moduleName in this.modules)
        {
            var instance = this.modules[moduleName];
            this._call(instance, name, args);
        }

        this.callback.trigger(name, args);
    },

    // private
    _call: function(instance, name, args)
    {
        if (typeof instance['onmessage'] !== 'undefined')
        {
            var arr = name.split('.');
            var func = instance['onmessage'][arr[0]];

            if (arr.length === 1 && typeof func === 'function')
            {
                func.apply(instance, args);
            }
            else if (arr.length === 2 && typeof func !== 'undefined' && typeof func[arr[1]] === 'function')
            {
                func[arr[1]].apply(instance, args);
            }
        }
    }
};
App.Builder = function(app)
{
    this.app = app;
    this.opts = app.opts;
};

App.Builder.prototype = {
    build: function()
    {
        this._buildServices();
        this._buildModules();
        this._buildPlugins();
    },

    // private
    _buildServices: function()
    {
        var services = [];
        for (var name in $RE.services)
        {
            if (this.app.coreServices.indexOf(name) === -1)
            {
                this.app[name] = $RE.create('service.' + name, this.app);
                this.app.bindableServices.push(name);
                services.push(name);
            }
        }

        // binding
        for (var i = 0; i < services.length; i++)
        {
            var service = services[i];
            for (var z = 0; z < this.app.bindableServices.length; z++)
            {
                var inj = this.app.bindableServices[z];
                if (service !== inj)
                {
                    this.app[service][inj] = this.app[inj];
                }
            }
        }

        this.app.services = services;
    },
    _buildModules: function()
    {
        for (var name in $RE.modules)
        {
            this.app.modules[name] = $RE.create('module.' + name, this.app);
        }
    },
    _buildPlugins: function()
    {
        var plugins = (this.opts.plugins) ? this.opts.plugins : [];
        for (var i = 0; i < plugins.length; i++)
        {
            var name = plugins[i];
            if (typeof $RE.plugins[name] !== 'undefined')
            {
                this.app.modules[name] = $RE.create('plugin.' + name, this.app);
            }
        }
    }
};
App.Starter = function(app)
{
    this.app = app;
    this.queue = {
        'start': app.queueStart,
        'stop': app.queueStop
    };
    this.priority = {
        'start': { 'service': [], 'module': [] },
        'stop': { 'service': [], 'module': [] }
    };
};

App.Starter.prototype = {
    start: function()
    {
        this._stopStart('service', 'start');
        this._stopStart('module', 'start');
    },
    stop: function()
    {
        this._stopStart('service', 'stop');
        this._stopStart('module', 'stop');
    },

    // private
    _stopStart: function(type, method)
    {
        // priority
        var queue = this.queue[method][type];
        for (var key in queue)
        {
            var name = queue[key];
            var instance = (type === 'service') ? this.app[name] : this.app.modules[name];

            this._call(instance, method);
            this.priority[method][type].push(name);
        }

        // common
        var modules = (type === 'service') ? this.app.services : this.app.modules;
        for (var key in modules)
        {
            var name = (type === 'service') ? modules[key] : key;

            if (this.priority[method][type].indexOf(name) === -1 && typeof name === 'string')
            {
                var instance = (type === 'service') ? this.app[name] : modules[name];
                this._call(instance, method);
            }
        }
    },
    _call: function(instance, method, args)
    {
        if (typeof instance[method] === 'function')
        {
            return instance[method].apply(instance, args);
        }
    }
};
App.Callback = function(app)
{
    this.app = app;
    this.opts = app.opts;

    // local
    this.callbacks = {};

    // build
    this._build();
};

App.Callback.prototype = {
    stop: function()
    {
        this.callbacks = {};
    },
    add: function(name, handler)
    {
        if (typeof this.callbacks[name] === 'undefined') this.callbacks[name] = [];

        this.callbacks[name].push(handler);
    },
    remove: function(name, handler)
    {
        if (handler === undefined)
        {
            delete this.callbacks[name];
        }
        else
        {
            for (var i = 0; i < this.callbacks[name].length; i++)
            {
                this.callbacks[name].splice(i, 1);
            }

            if (this.callbacks[name].length === 0)
            {
                delete this.callbacks[name];
            }
        }
    },
    trigger: function(name, args)
    {
        if (typeof this.callbacks[name] === 'undefined') return;

        for (var i = 0; i < this.callbacks[name].length; i++)
        {
            this.callbacks[name][i].apply(this.app, args);
        }
    },

    // private
    _build: function()
    {
        if (this.opts.callbacks)
        {
            for (var name in this.opts.callbacks)
            {
                if (typeof this.opts.callbacks[name] === 'function')
                {
                    if (typeof this.callbacks[name] === 'undefined') this.callbacks[name] = [];
                    this.callbacks[name].push(this.opts.callbacks[name]);
                }
                else
                {
                    for (var key in this.opts.callbacks[name])
                    {
                        if (typeof this.callbacks[name + '.' + key] === 'undefined') this.callbacks[name + '.' + key] = [];
                        this.callbacks[name + '.' + key].push(this.opts.callbacks[name][key]);
                    }

                }
            }
        }
    }
};
$RE.add('extend', 'dom', $RE.Dom.prototype);
$RE.add('extend', 'template', {
    init: function(app, props, type)
    {
        this.app = app;
        this.opts = app.opts;
        this.utils = app.utils;
        this.template = app.template;
        this.props = props;

        // local
        this.randomId = this.utils.getRandomId();

        // build
        this.build();
        this.dataset('instance', this);

        this.attr('data-element-id', this.randomId);
        this.attr('data-element-type', type);
    },
    setContainer: function($el)
    {
        this.$container = $el;
    },
    getContainer: function()
    {
        return this.$container;
    },
    getSource: function()
    {
        return this.props.$source;
    },
    setAttr: function($target, name, value)
    {
        var tagName = $target.get().tagName.toLowerCase();

        switch (name)
        {
            case 'background-color':

                $target.css('background-color', value);

                if (tagName !== 'a')
                {
                    $target.attr('bgcolor', value);
                }

                break;
            case 'background-image':

                $target.css('background-image', 'url(' + value + ')');
                $target.attr('background', value);

            case 'height':

                $target.attr('height', this._normalizeWidthAttr(value));
                $target.css('height', value);

                break;
            case 'color':
            case 'border':
            case 'font-size':
            case 'font-weight':
            case 'font-family':
            case 'line-height':
            case 'background-size':
            case 'text-decoration':
            case 'text-transform':
            case 'border-radius':
            case 'padding':

                $target.css(name, value);

                break;
            case 'align':

                $target.css('text-align', value);

                if (tagName === 'td')
                {
                    $target.attr('align', value);
                }

                break;
            case 'valign':

                $target.css('vertical-align', value);

                if (tagName === 'td')
                {
                    $target.attr('valign', value);
                }

                break;
            case 'class':

                $target.addClass(value);

                break;
            case 'direction':
            case 'src':
            case 'href':
            case 'alt':

                $target.attr(name, value);

                break;

            case 'width':

                $target.attr(name, this._normalizeWidthAttr(value));
                $target.css(name, value);

                break;
        }

        return $target;
    },
    injectAttr: function($target, $source, except)
    {
        var attrs = this._getAttrs($source);
        for (var name in attrs)
        {
            if (except && except.indexOf(name) !== -1) continue;

            $target = this.setAttr($target, name, attrs[name]);
        }

        return $target;
    },
    createTable: function(props)
    {
        var $table = $RE.dom('<table>');
        $table.attr({
            'cellpadding': 0,
            'cellspacing': 0,
            'border': 0
        });

        if (props && props.attrs) $table.attr(props.attrs);
        if (props && props.css) $table.css(props.css);

        return $table;
    },
    createImage: function()
    {
        return $RE.dom('<img>').attr('border', 0).css({ 'margin': 0, 'padding': 0, 'max-width': '100%', 'border': 'none' });
    },

    // private
    _setBackgroundColor: function(value, $target, $source)
    {
        if (value === 'none')
        {
            $target.css('background-color', '');
            $target.removeAttr('bgcolor');
            $source.removeAttr('background-color');
        }
        else
        {
            $target.css('background-color', value);
            $target.attr('bgcolor', value);
            $source.attr('background-color', value);
        }
    },
    _getTextLineHeight: function($source)
    {
        if ($source.attr('font-size') && !$source.attr('line-height'))
        {
            return (parseInt($source.attr('font-size')) * this.opts.textLineHeight) + 'px';
        }

        return this.opts.styles.text['line-height'];
    },
    _getHeadingLineHeight: function(fontSize)
    {
        return Math.round(parseInt(fontSize) * this.opts.headingsLineHeight);
    },
    _parseLinks: function(html)
    {
        var $wrapper = $RE.dom('<div>');
        $wrapper.html(html);

        $wrapper.find('re-link').each(function(node)
        {
            var $node = $RE.dom(node);
            var instance = $RE.create('template.link', this.app, { $source: $node });

            $node.replaceWith(instance);

        }.bind(this));

        var html = $wrapper.html();
        $wrapper.remove();

        return html;
    },
    _normalizeWidthAttr: function(width)
    {
        return (width.search('%') !== -1) ? width : parseInt(width);
    },
    _getAttrs: function($source)
    {
        var el = $source.get();
        var attrs = {};
        for (var i = 0, atts = el.attributes, n = atts.length, arr = []; i < n; i++)
        {
            attrs[atts[i].nodeName] = atts[i].nodeValue;
        }

        return attrs;
    }
});
$RE.add('service', 'element', {
    init: function(app)
    {
        this.app = app;
        this.$element = $RE.dom(this.app.rootElement);
    },
    start: function()
    {
        this._build();
    },
    getElement: function()
    {
        return this.$element;
    },

    // private
    _build: function()
    {
        this.$element.addClass('re-editor');
    }
});
$RE.add('service', 'animate', {
    init: function(app)
    {
        this.animationOpt = app.opts.animation;
    },
    start: function(element, animation, options, callback)
    {
        var defaults = {
            duration: false,
            iterate: false,
            delay: false,
            timing: false,
            prefix: 'remail-'
        };

        defaults = (typeof options === 'function') ? defaults : $RE.extend(defaults, options);
        callback = (typeof options === 'function') ? options : callback;

        // play
        return new $RE.AnimatePlay(element, animation, defaults, callback, this.animationOpt);
    },
    stop: function(element)
    {
        this.$el = $RE.dom(element);
        this.$el.removeClass('remail-animated');

        var effect = this.$el.attr('remail-animate-effect');
        this.$el.removeClass(effect);

        this.$el.removeAttr('remail-animate-effect');
        var hide = this.$el.attr('remail-animate-hide');
        if (hide)
        {
            this.$el.addClass(hide).removeAttr('remail-animate-hide');
        }

        this.$el.off('animationend webkitAnimationEnd');
    }
});

$RE.AnimatePlay = function(element, animation, defaults, callback, animationOpt)
{
    this.hidableEffects = ['fadeOut', 'flipOut', 'slideUp', 'zoomOut', 'slideOutUp', 'slideOutRight', 'slideOutLeft'];
    this.prefixes = ['', '-webkit-'];

    this.$el = $RE.dom(element);
    this.$body = $RE.dom('body');
    this.callback = callback;
    this.animation = (!animationOpt) ? this.buildAnimationOff(animation) : animation;
    this.defaults = defaults;

    if (this.animation === 'slideUp')
    {
        this.$el.height(this.$el.height());
    }

    // animate
    return (this.isInanimate()) ? this.inanimate() : this.animate();
};

$RE.AnimatePlay.prototype = {
    buildAnimationOff: function(animation)
    {
        return (this.isHidable(animation)) ? 'hide' : 'show';
    },
    buildHideClass: function()
    {
        return 'remail-animate-hide';
    },
    isInanimate: function()
    {
        return (this.animation === 'show' || this.animation === 'hide');
    },
    isAnimated: function()
    {
        return this.$el.hasClass('remail-animated');
    },
    isHidable: function(effect)
    {
        return (this.hidableEffects.indexOf(effect) !== -1);
    },
    inanimate: function()
    {
        this.defaults.timing = 'linear';

        var hide;
        if (this.animation === 'show')
        {
            hide = this.buildHideClass();
            this.$el.attr('remail-animate-hide', hide);
            this.$el.removeClass(hide);
        }
        else
        {
            hide = this.$el.attr('remail-animate-hide');
            this.$el.addClass(hide).removeAttr('remail-animate-hide');
        }

        if (typeof this.callback === 'function') this.callback(this);

        return this;
    },
    animate: function()
    {
        var delay = (this.defaults.delay) ? this.defaults.delay : 0;
        setTimeout(function()
        {
            this.$body.addClass('no-scroll-x');
            this.$el.addClass('remail-animated');
            if (!this.$el.attr('remail-animate-hide'))
            {
                var hide = this.buildHideClass();
                this.$el.attr('remail-animate-hide', hide);
                this.$el.removeClass(hide);
            }

            this.$el.addClass(this.defaults.prefix + this.animation);
            this.$el.attr('remail-animate-effect', this.defaults.prefix + this.animation);

            this.set(this.defaults.duration + 's', this.defaults.iterate, this.defaults.timing);
            this.complete();

        }.bind(this), delay * 1000);

        return this;
    },
    set: function(duration, iterate, timing)
    {
        var len = this.prefixes.length;

        while (len--)
        {
            if (duration !== false || duration === '') this.$el.css(this.prefixes[len] + 'animation-duration', duration);
            if (iterate !== false || iterate === '') this.$el.css(this.prefixes[len] + 'animation-iteration-count', iterate);
            if (timing !== false || timing === '') this.$el.css(this.prefixes[len] + 'animation-timing-function', timing);
        }
    },
    clean: function()
    {
        this.$body.removeClass('no-scroll-x');
        this.$el.removeClass('remail-animated');
        this.$el.removeClass(this.defaults.prefix + this.animation);
        this.$el.removeAttr('remail-animate-effect');

        this.set('', '', '');
    },
    complete: function()
    {
        this.$el.one('animationend webkitAnimationEnd', function()
        {
            if (this.$el.hasClass(this.defaults.prefix + this.animation)) this.clean();
            if (this.isHidable(this.animation))
            {
                var hide = this.$el.attr('remail-animate-hide');
                this.$el.addClass(hide);
            }

            this.$el.removeAttr('remail-animate-hide');

            if (this.animation === 'slideUp') this.$el.height('');
            if (typeof this.callback === 'function') this.callback(this.$el);

        }.bind(this));
    }
};
$RE.add('service', 'options', {
    init: function(app, element, options)
    {
        var $el = $RE.dom(element);
        var opts = $RE.extend({}, $RE.opts, (element) ? $el.data() : {}, $RE.options);
        opts = $RE.extend(true, opts, options);

        return opts;
    }
});
$RE.add('service', 'lang', {
    init: function(app)
    {
        this.app = app;
        this.opts = app.opts;

        var lang = (this.opts.lang) ? this.opts.lang : 'en';

        // build
        this.vars = this.build(lang);
    },
	build: function(lang)
	{
    	lang = ($RE.lang[lang] === undefined) ? 'en' : lang;

        return ($RE.lang[lang] !== undefined) ? $RE.lang[lang] : [];
	},
    rebuild: function(lang)
    {
        this.opts.lang = lang;
        this.vars = this.build(lang);
    },
    extend: function(obj)
    {
        this.vars = $RE.extend(this.vars, obj);
    },
    parse: function(str)
    {
        if (str === undefined)
        {
            return '';
        }

        var matches = str.match(/## (.*?) ##/g);
        if (matches)
        {
            for (var i = 0; i < matches.length; i++)
            {
                var key = matches[i].replace(/^##\s/g, '').replace(/\s##$/g, '');
                str = str.replace(matches[i], this.get(key));
            }
        }

        return str;
    },
	get: function(name)
	{
		return (typeof this.vars[name] !== 'undefined') ? this.vars[name] : '';
	}
});
$RE.add('service', 'utils', {
    init: function(app)
    {
        this.app = app;

    },

    // string
    ucfirst: function(str)
    {
        return str.charAt(0).toUpperCase() + str.substr(1);
    },

    // random
    getRandomId: function(appendix)
    {
        var id = '';
        var possible = 'abcdefghijklmnopqrstuvwxyz0123456789';

        for (var i = 0; i < 12; i++)
        {
            id += possible.charAt(Math.floor(Math.random() * possible.length));
        }

        return id + ((typeof appendix === 'undefined') ? '' : appendix);
    },

    // scroll
    scrollTo: function (element, to, duration, callback)
    {
        var easeInOutQuad = function(t, b, c, d)
        {
            t /= d/2;
            if (t < 1) return c/2*t*t + b;
        	t--;
        	return -c/2 * (t*(t-2) - 1) + b;
        };

        var $el = $RE.dom(element);
        var start = $el.scrollTop();
        var change = to - start;
        var currentTime = 0;
        var increment = 20;

        var animate = function()
        {
            currentTime += increment;
            var val = easeInOutQuad(currentTime, start, change, duration);

            $el.scrollTop(val);

            if (currentTime < duration)
            {
                setTimeout(animate, increment);
            }
            else
            {
                if (typeof callback === 'function') callback();
            }
        };

        animate();
    },


    // color
    replaceRgbToHex: function(html)
    {
        return html.replace(/rgb\((.*?)\)/g, function (match, capture)
        {
            var a = capture.split(',');
            var b = a.map(function(x)
            {
                x = parseInt(x).toString(16);
                return (x.length === 1) ? '0' + x : x;
            });

            return '#' + b.join("");
        });
    },
    getHexValue: function(val)
    {
        if (typeof val === 'undefined' || val == null) return '#000000';

        val = (this.isRgb(val)) ? this.rgb2hex(val) : '#' + val.replace('#', '');

        return val;
    },
    isRgb: function(str)
    {
        return (str.search(/^rgb/i) === 0);
    },
    rgb2hex: function(rgb)
    {
        rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);

        return (rgb && rgb.length === 4) ? "#" +
        ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
    },
    hex2long: function(val)
    {
        if (val.search(/^#/) !== -1 && val.length === 4)
        {
            val = '#' + val[1] + val[1] + val[2] + val[2] + val[3] + val[3];
        }

        return val;
    },
    hex2short: function(val)
    {
        var hex = val.replace('#', '');
        if ((hex.charAt(0) == hex.charAt(1)) && (hex.charAt(2) == hex.charAt(3)) && (hex.charAt(4) == hex.charAt(5)))
        {
            val = "#" + hex.charAt(0) + hex.charAt(2) + hex.charAt(4);
        }

        return val;
    }
});
$RE.add('service', 'frame', {
    init: function(app)
    {
        this.app = app;

        // local
        this.cssfile = 'css/revolvapp-frame.min.css?' + new Date();
    },
    start: function()
    {
        this._build();
    },
    load: function(func)
    {
        var $doc = this.getDoc();
        var timer = setInterval(function()
        {
            var iframeDoc = $doc.get();
            if (iframeDoc.readyState == 'complete' || iframeDoc.readyState == 'interactive')
            {
                func();
                this.loadImages();
                clearInterval(timer);
                return;
            }
        }.bind(this), 100);
    },
    loadImages: function()
    {
        var $doc = this.getDoc();
        var $images = $doc.find('img');
        var totalImg = $images.length;
        var self = this;

        $images.each(function(img)
        {
            if (self.opts.images)
            {
                var arr = img.src.split('/');
                var last = arr[arr.length-1];
                img.src = self.opts.images + last;
            }

            img.onload = function()
            {
                totalImg--;
            };
        });

        var timer = setInterval(function()
        {
            if (totalImg === 0)
            {
                this.app.broadcast('adjustHeight', this);
                clearInterval(timer);
                return;
            }
        }.bind(this), 50);
    },
    buildCss: function()
    {
        var $head = this.getHead();
        var $css = $RE.dom('<link>');
        $css.attr({
            'id': 're-css',
            'rel': 'stylesheet',
            'href': this.opts.path + this.cssfile
        });

        $head.append($css);
    },
    buildUndo: function()
    {
        var $doc = this.getDoc();
        $doc.on('keydown.revolvapp', function(e)
        {
            var key = e.which;
            var isCtrl = (e.ctrlKey || e.metaKey);
            var $target = $RE.dom(e.target);

            if ($target.hasClass('re-editable')) return;

            // z
            if (isCtrl && key === 90)
            {
                this.template.revertSnapshot();
            }

        }.bind(this));
    },
    buildClick: function()
    {
        this.getBody().on('click.revolvapp.control', this._handleBodyClick.bind(this));
    },

    // adjust
    adjustHeight: function(value)
    {
        setTimeout(function()
        {
            var height = this.getBody().height() + (value || 0);

            height = (height < 140) ? 140 : height;

            var $dropdown = this.getBody().find('#re-dropdown');
            if ($dropdown.length !== 0)
            {
                var dropdownHeight = $dropdown.height();
                var pos = $dropdown.offset();

                height = (height < dropdownHeight) ? (pos.top + dropdownHeight + 20) : height;
            }


            this.$frame.height(height);

        }.bind(this), 100);

    },

    // set
    setCode: function(html)
    {
        var $doc = this.getDoc();
        var doc = $doc.get();

        // write html
    	doc.open();
		doc.write(html);
		doc.close();
    },
    setTemplate: function(code)
    {
        this.settedCode = code;

        this.setSource(code);
        this.setCode(this.template.getInitialCode());
        this.load(this._load.bind(this));
    },
    setSource: function(html)
    {
        this.frameSource = html;
    },

    // get
    getElement: function()
    {
        return this.$frame;
    },
    getDoc: function()
    {
        return $RE.dom(this.$frame.get().contentWindow.document);
    },
    getWin: function()
    {
        return this.$frame.get().contentWindow;
    },
    getHead: function()
    {
        return $RE.dom(this.$frame.get().contentWindow.document).find('head');
    },
    getBody: function()
    {
        return $RE.dom(this.$frame.get().contentWindow.document).find('body');
    },
    getCards: function()
    {
        return this.getBody().find('.re-card');
    },
    getControls: function(type, id)
    {
        var $controls = this.getBody().find('.re-controls-' + type);
        var filter = function(node) { return ($RE.dom(node).attr('data-control-id') === id) };

        return (typeof id === 'undefined') ? $controls : $controls.filter(filter);
    },
    getSource: function(tidy)
    {
        var code = this.frameSource;
        code = this.utils.replaceRgbToHex(code);

        return (tidy === true) ? this.tidy.get(code) : code;
    },
    getTemplate: function(tidy)
    {
        var code = this.template.getSource();

        return (tidy === true) ? this.tidy.get(code) : code;
    },
    getHtml: function(tidy)
    {
        var $doc = this.getDoc().clone();
        var doc = $doc.get();

        // clean
        this._removeUtils($doc);
        this._removeDataAttrs($doc);
        this._removeEditable($doc);
        this._removeClasses($doc);

        var html = doc.documentElement.outerHTML;

        if (tidy === true)
        {
            html = this.tidy.get(html, 'html');
        }

        html = html.replace(/url\(&quot;(.*?)&quot;\)/gi, "url('$1')");
        html = html.replace(/&quot;(.*?)&quot;/gi, "'$1'");
        html = this.utils.replaceRgbToHex(html);

        // html
        return this._getDoctype(doc) + '\n' + html;
    },

    // private
    _build: function()
    {
        this.$frame = $RE.dom('<iframe>');

        // append
        var $element = this.element.getElement();
        $element.append(this.$frame);
    },
    _handleBodyClick: function(e)
    {
        var $target = $RE.dom(e.target);
        var isCard = ($target.closest('.re-card').length === 1);
        var isDropdown = ($target.closest('#re-dropdown').length === 1);
        var isColorPicker = ($target.closest('#re-color-picker').length === 1);
        var isSlide = ($target.closest('#re-slide').length === 1);

        if (!isCard && !isDropdown && !isColorPicker && !isSlide)
        {
            this.control.hide('block');
            this.control.show('card');

            this.getBody().find('.re-block').removeClass('is-re-active');
        }
    },
    _removeUtils: function($doc)
    {
        $doc.find('#re-css, #re-add-block-icon, #re-editor-toolbar, #re-editor-toolbar-helper, #re-color-picker, #re-slide, #re-dropdown, .re-controls').remove();
        $doc.find('body').find('.re-card, .re-block').removeClass('is-re-active is-re-readonly');
    },
    _removeClasses: function($doc)
    {
        var classes = ['re-editable', 're-card', 're-block'];

        $doc.find('body').find('.' + classes.join(', .')).removeClass(classes.join(' '));
    },
    _removeEditable: function($doc)
    {
        $doc.find('body').find('.re-editable').removeAttr('contenteditable');
    },
    _removeDataAttrs: function($doc)
    {
        $doc.find('[ondragstart]').removeAttr('ondragstart');
        $doc.find('[data-element-id]').removeAttr('data-element-id data-element-type');
    },
    _getDoctype: function(doc)
    {
        var node = doc.doctype;

        return "<!DOCTYPE " + node.name
         + (node.publicId ? ' PUBLIC "' + node.publicId + '"' : '')
         + (!node.publicId && node.systemId ? ' SYSTEM' : '')
         + (node.systemId ? ' "' + node.systemId + '"' : '') + '>';
    },
    _load: function()
    {
        this.template.build(this.settedCode);

        // built
        setTimeout(function()
        {
            this.buildCss();
            this.buildUndo();
            this.buildClick();

            this.app.broadcast('rebuilt', this);
            this.app.broadcast('images.observe', this);
            this.app.broadcast('adjustHeight', this);


        }.bind(this), 100);
    }
});
$RE.add('service', 'panel', {
    init: function(app)
    {
        this.app = app;

        // local
        this.activeClass = 'is-re-editor-panel-active';
        this.disableClass = 'is-re-editor-panel-disable';
    },
    build: function()
    {
        this.$panel = $RE.dom('<div>');
        this.$panel.attr('id', 're-editor-panel');
        this.$panel.addClass('re-editor-panel');

        this.$panelLeft = $RE.dom('<div>');
        this.$panelLeft.addClass('re-editor-panel-left');

        this.$panelRight = $RE.dom('<div>');
        this.$panelRight.addClass('re-editor-panel-right');

        this.$panel.append(this.$panelLeft);
        this.$panel.append(this.$panelRight);

        // prepend
        var $element = this.element.getElement();
        $element.prepend(this.$panel);
    },

    // public
    add: function(button, side)
    {
        var $target = (side === 'left') ? this.$panelLeft : this.$panelRight;
        var $button = this._createButton(button);

        if (side === 'left') $target.append($button);
        else $target.prepend($button);
    },
    remove: function(name)
    {
        var $button = this._findButton(name);
        $button.remove();
    },
    addActive: function(name)
    {
        var $buttons = this.$panel.find('span');
        $buttons.removeClass(this.activeClass);

        var $button = this._findButton(name);
        $button.addClass(this.activeClass);
    },
    disableButtons: function(except)
    {
        var $buttons = this.$panel.find('span');
        $buttons.each(function(node)
        {
            var $btn = $RE.dom(node);
            var btnName = $btn.attr('data-name');

            if (except.indexOf(btnName) === -1)
            {
                $btn.addClass(this.disableClass);
            }

        }.bind(this));
    },
    enableButtons: function()
    {
        var $buttons = this.$panel.find('span');
        $buttons.removeClass(this.disableClass);
    },

    // private
    _createButton: function(button)
    {
        var self = this;
        var $button = $RE.dom('<span>');

        $button.attr('data-name', button.name);
        $button.html(button.icon);
        $button.on('click', function(e)
        {
            e.preventDefault();

            var $btn = $RE.dom(e.target).closest('span');
            var name = $btn.attr('data-name');

            if ($btn.hasClass(self.disableClass)) return;

            button.callback(name);
        });

        return $button;
    },
    _findButton: function(name)
    {
        return this.$panel.find('[data-name=' + name + ']');
    }
});
$RE.add('service', 'control', {
    init: function(app)
    {
        this.app = app;

        // local
        this.icons = {
            card: ['settings', 'duplicate', 'trash'],
            block: ['settings', 'trash', 'duplicate', 'add']
        };
    },
    build: function(type, $el, first)
    {
        this.$el = $el;
        this.controlType = type;
        this.cardFirst = first;

        // build
        this._buildContainer();
        this._buildIcons();

        // append
        this._appendToBody();
        this._setPosition();
    },
    remove: function(type, id)
    {
        this.frame.getControls(type, id).remove();
    },
    show: function(type, id)
    {
        var $controls = this.frame.getControls(type, id);

        if (type === 'card')
        {
            var self = this;
            $controls.each(function(node)
            {
                var $control = $RE.dom(node);
                var id = $control.attr('data-control-id');
                var $card = self.frame.getBody().find('[data-element-id="' + id + '"]');
                var $blocks = $card.find('.re-block');

                if ($blocks.length !== 0)
                {
                    $control.show();
                }
            });
        }
        else
        {
            $controls.show();
        }
    },
    hide: function(type, id)
    {
        this.frame.getControls(type, id).hide();
    },

    // private
    _buildContainer: function()
    {
        this.$control = $RE.dom('<div>');
        this.$control.addClass('re-controls re-controls-' + this.controlType);
        this.$control.attr('data-control-id', this.$el.attr('data-element-id'));

        if (this.controlType === 'card')
        {
            this.$control.on('mouseover', function(e)
            {
                var $control = $RE.dom(e.target).closest('.re-controls');
                var id = $control.attr('data-control-id');
                var $el = this.frame.getBody().find('[data-element-id="' + id + '"]');

                $el.addClass('is-re-hover');

            }.bind(this));

            this.$control.on('mouseout', function(e)
            {
                var $control = $RE.dom(e.target).closest('.re-controls');
                var id = $control.attr('data-control-id');
                var $el = this.frame.getBody().find('[data-element-id="' + id + '"]');

                $el.removeClass('is-re-hover');

            }.bind(this));
        }
    },
    _buildIcons: function()
    {
        var icons = this.icons[this.controlType];
        var options = this.opts[this.controlType];
        for (var i = 0; i < icons.length; i++)
        {
            if (typeof options[icons[i]] !== 'undefined' && options[icons[i]] === false) continue;

            // do not show the trash icon for the first card
            if (this.controlType === 'card' && this.cardFirst && icons[i] === 'trash') continue;

            this._buildIcon(icons[i]);
        }
    },
    _buildIcon: function(name)
    {
        var $icon = $RE.dom('<span>');

        $icon.addClass('re-icon-control re-icon-control-' + name);
        $icon.html(this.opts.icons[name]);

        $icon.attr('data-icon-name', name);
        $icon.attr('data-control-type', this.controlType);
        $icon.attr('data-id', this.$el.attr('data-element-id'));

        $icon.on('click', this._handleClick.bind(this));

        // append
        this.$control.append($icon);
    },
    _handleClick: function(e)
    {
        e.preventDefault();
        e.stopPropagation();

        var $icon = $RE.dom(e.target).closest('.re-icon-control');
        var type = $icon.attr('data-control-type');
        var name = $icon.attr('data-icon-name');
        var id = $icon.attr('data-id');
        var $el = this.frame.getBody().find('[data-element-id="' + id + '"]');

        this.app.broadcast(type + '.' + name, this, e, $el, $icon);
    },
    _appendToBody: function()
    {
        this.frame.getBody().append(this.$control);
    },
    _setPosition: function()
    {
        var bodyWidth = this.frame.getBody().width();
        var minWidth = 680;
        var $target = (this.controlType === 'card') ? this.$el : this.$el.closest('.re-card');
        var width = $target.innerWidth();
        var height = this.$el.innerHeight();
        var offset = $target.offset();
        var elOffset = this.$el.offset();
        var shift = (bodyWidth < minWidth) ? -40 : 4;
        var top = elOffset.top + 'px';
        var left = (offset.left + width + shift) + 'px';

        this.$control.css({
            'top': top,
            'left': left
        });
    }
});
$RE.add('service', 'dropdown', {
    init: function(app)
    {
        this.app = app;
    },
    build: function($el, $html)
    {
        var $body = this.frame.getBody();
        var $dropdown = $body.find('#re-dropdown');

        if ($dropdown.length !== 0)
        {
            return this.close($dropdown);
        }

        this.$dropdown = $RE.dom('<div>');
        this.$dropdown.attr('id', 're-dropdown');
        this.$dropdown.addClass('re-dropdown');
        this.$dropdown.html('');
        this.$dropdown.append($html);

        // position
        var offset = $el.offset();
        var width = $el.width();
        var outlineShift = 2;
        var dropdownWidth = 270;

        this.$dropdown.css({
            top: offset.top + 'px',
            left: ((offset.left + width) - 270 + outlineShift) + 'px'
        });

        $body.append(this.$dropdown);
        $body.on('click.revolvapp.dropdown', this._close.bind(this));
        $body.on('keydown.revolvapp.dropdown', this._close.bind(this));

        this.app.broadcast('adjustHeight', this);
    },
    close: function($dropdown)
    {
        var $body = this.frame.getBody();
        var $dropdown = $dropdown || $body.find('#re-dropdown');

        $dropdown.remove();
        $body.off('.revolvapp.dropdown');

        this.frame.getCards().removeClass('is-re-active');
        this.app.broadcast('adjustHeight', this);
    },

    // private
    _close: function(e)
    {
        if (e)
        {
            if (e.type === 'keydown')
            {
                if (e.which === 27) this.close();
            }
            else
            {
                var $target = $RE.dom(e.target);
                var isDropdown = ($target.closest('#re-dropdown').length === 1);
                var isColorPicker = ($target.closest('#re-color-picker').length === 1);

                if (!isDropdown && !isColorPicker)
                {
                    this.close();
                }
            }
        }
    }
});
$RE.add('service', 'slide', {
    init: function(app)
    {
        this.app = app;
    },
    build: function($el, $html)
    {
        this.$el = $el;
        this.$html = $html;

        if (this.$el.hasClass('slide-in'))
        {
            return this._close();
        }

        this._closeAll();
        this._build();
        this._open();
    },
    close: function()
    {
        this._closeAll();
        this.app.broadcast('adjustHeight', this);
    },

    // private
    _build: function()
    {
        this.$slide = $RE.dom('<div>');
        this.$slide.attr('id', 're-slide');
        this.$slide.addClass('re-slide');
        this.$slide.addClass('remail-animate-hide');
        this.$slide.append(this.$html);

        this.$el.after(this.$slide);
    },

    // open
    _open: function()
    {
        this.animate.start(this.$slide, 'slideDown', this._opened.bind(this));
    },
    _opened: function()
    {
        this.$el.addClass('slide-in');
        this.app.broadcast('adjustHeight', this);

        var $body = this.frame.getBody();

        $body.on('click.revolvapp.slide', this._close.bind(this));
        $body.on('keydown.revolvapp.slide', this._close.bind(this));
    },

    // close
    _close: function(e)
    {
        var $slide = this.$slide || this.frame.getBody().find('#re-slide');

        if (e)
        {
            if (e.type === 'keydown')
            {
                if (e.which === 27) this.animate.start($slide, 'slideUp', this._closed.bind(this));
            }
            else
            {
                var $target = $RE.dom(e.target);
                var isAddButton = ($target.closest('#re-add-block-icon').length === 1);
                var isSlide = ($target.closest('#re-slide').length === 1);
                var isColorPicker = ($target.closest('#re-color-picker').length === 1);

                if (!isAddButton && !isSlide && !isColorPicker)
                {
                    this.animate.start($slide, 'slideUp', this._closed.bind(this));
                }
            }
        }
        else
        {

            this.animate.start($slide, 'slideUp', this._closed.bind(this));
        }
    },
    _closed: function($slide)
    {
        // remove
        $slide.remove();

        this.$el.removeClass('slide-in');
        this.app.broadcast('adjustHeight', this);
        this.app.broadcast('setControlPosition', this);

        var $body = this.frame.getBody();
        $body.off('.revolvapp.slide');
    },
    _closeAll: function()
    {
        var $body = this.frame.getBody();

        $body.find('#re-slide').remove();
        $body.find('.slide-in').removeClass('slide-in');
    }
});
$RE.add('service', 'tidy', {
    init: function(app)
    {
        this.app = app;
    },
    get: function(code, type)
	{
        code = code.replace(/\t/g, '    ');
        code = code.replace(/<!\-\-\s+</g, '<!-- <');
        code = code.replace(/\n/g, '');

    	// clean setup
    	var ownLine = ['re-style', 'style', 'meta', 'link'];
    	var contOwnLine = [];
    	var newLevel = ['re-container', 're-title', 're-social-link', 're-font', 're-link', 're-divider',
    	                're-menu-link', 're-spacer', 're-text', 're-heading', 're-image-link', 're-image',
    	                're-column-spacer', 're-inline-spacer', 're-body', 're-head', 're-header', 're-footer',
    	                're-main', 're-card', 're-block', 're-grid', 're-row', 're-column', 're-preheader',
    	                're-button', 're-button-link', 're-button-app'];

        if (type === 'html')
        {
            newLevel = ['p', 'title', 'head', 'body', 'table', 'thead', 'tbody', 'tfoot', 'tr', 'td', 'th'];
        }

    	this.lineBefore = new RegExp('^<(/?' + ownLine.join('|/?' ) + '|' + contOwnLine.join('|') + ')[ >]');
    	this.lineAfter = new RegExp('^<(br|/?' + ownLine.join('|/?' ) + '|/' + contOwnLine.join('|/') + ')[ >]');
    	this.newLevel = new RegExp('^</?(' + newLevel.join('|' ) + ')[ >]');

    	var i = 0,
    	codeLength = code.length,
    	point = 0,
    	start = null,
    	end = null,
    	tag = '',
    	out = '',
    	cont = '';

    	this.cleanlevel = 0;

    	for (; i < codeLength; i++)
    	{
    		point = i;

    		// if no more tags, copy and exit
    		if (-1 == code.substr(i).indexOf( '<' ))
    		{
    			out += code.substr(i);

    			return this.finish(out, type);
    		}

    		// copy verbatim until a tag
    		while (point < codeLength && code.charAt(point) != '<')
    		{
    			point++;
    		}

    		if (i != point)
    		{
    			cont = code.substr(i, point - i);
    			if (!cont.match(/^\s{2,}$/g))
    			{
    				if ('\n' == out.charAt(out.length - 1)) out += this.getTabs();
    				else if ('\n' == cont.charAt(0))
    				{
    					out += '\n' + this.getTabs();
    					cont = cont.replace(/^\s+/, '');
    				}

    				out += cont;
    			}

    			if (cont.match(/\n/)) out += '\n' + this.getTabs();
    		}

    		start = point;

    		// find the end of the tag
    		while (point < codeLength && '>' != code.charAt(point))
    		{
    			point++;
    		}

    		tag = code.substr(start, point - start);
    		i = point;

    		var t;

    		if ('!--' == tag.substr(1, 3))
    		{
    			if (!tag.match(/--$/))
    			{
    				while ('-->' != code.substr(point, 3))
    				{
    					point++;
    				}
    				point += 2;
    				tag = code.substr(start, point - start);
    				i = point;
    			}

    			if ('\n' != out.charAt(out.length - 1)) out += '\n';

    			out += this.getTabs();
    			out += tag + '>\n';
    		}
    		else if ('!' == tag[1])
    		{
    			out = this.placeTag(tag + '>', out);
    		}
    		else if ('?' == tag[1])
    		{
    			out += tag + '>\n';
    		}
    		else if (t = tag.match(/^<(script|style|pre)/i))
    		{
    			t[1] = t[1].toLowerCase();
    			tag = this.cleanTag(tag);
    			out = this.placeTag(tag, out);
    			end = String(code.substr(i + 1)).toLowerCase().indexOf('</' + t[1]);

    			if (end)
    			{
    				cont = code.substr(i + 1, end);
    				i += end;
    				out += cont;
    			}
    		}
    		else
    		{
    			tag = this.cleanTag(tag);
    			out = this.placeTag(tag, out);
    		}
    	}

    	return this.finish(out, type);
    },
    getTabs: function()
    {
    	var s = '';
    	for ( var j = 0; j < this.cleanlevel; j++ )
    	{
    		s += '    ';
    	}

    	return s;
    },
    finish: function(code, type)
    {
    	code = code.replace(/\n\s*\n/g, '\n');
    	code = code.replace(/^[\s\n]*/, '');
    	code = code.replace(/[\s\n]*$/, '');
    	code = code.replace(/<script(.*?)>\n<\/script>/gi, '<script$1></script>');

    	this.cleanlevel = 0;

    	if (type !== 'html')
    	{
            var closeTags = ['re-font', 're-social-link', 're-mobile-spacer', 're-inline-spacer', 're-column-spacer', 're-image',
                             're-divider', 're-spacer', 're-button-app'];
            var re = new RegExp('>\\n\\s+</(' + closeTags.join('|') + ')>', 'g');

            code = code.replace(re, '></$1>');
    	}

    	return code;
    },
    cleanTag: function (tag)
    {
    	var tagout = '';
    	tag = tag.replace(/\n/g, ' ');
    	tag = tag.replace(/\s{2,}/g, ' ');
    	tag = tag.replace(/^\s+|\s+$/g, ' ');

    	var suffix = '';
    	if (tag.match(/\/$/))
    	{
    		suffix = '/';
    		tag = tag.replace(/\/+$/, '');
    	}

    	var m;
    	while (m = /\s*([^= ]+)(?:=((['"']).*?\3|[^ ]+))?/.exec(tag))
    	{
    		if (m[2]) tagout += m[1].toLowerCase() + '=' + m[2];
    		else if (m[1]) tagout += m[1].toLowerCase();

    		tagout += ' ';
    		tag = tag.substr(m[0].length);
    	}

    	return tagout.replace(/\s*$/, '') + suffix + '>';
    },
    placeTag: function (tag, out)
    {
    	var nl = tag.match(this.newLevel);

    	if (tag.match(this.lineBefore) || nl)
    	{
    		out = out.replace(/\s*$/, '');
    		out += '\n';
    	}

    	if (nl && '/' == tag.charAt(1)) this.cleanlevel--;
    	if ('\n' == out.charAt(out.length - 1)) out += this.getTabs();
    	if (nl && '/' != tag.charAt(1)) this.cleanlevel++;

    	out += tag;

    	if (tag.match(this.lineAfter) || tag.match(this.newLevel))
    	{
    		out = out.replace(/ *$/, '');
    		out += '\n';
    	}

    	return out;
    }
});
$RE.add('service', 'editable', {
    init: function(app)
    {
        this.app = app;
        this.$doc = app.$doc;
    },
    build: function(el)
    {
        $RE.create('editable.class', this.app, el);
    }
});
$RE.add('class', 'editable.class', {
    init: function(app, el)
    {
        this.app = app;
        this.opts = app.opts;
        this.$el = $RE.dom(el);
        this.frame = app.frame;
        this.template = app.template;

        this.build();
    },
    build: function()
    {
        this.win = this.frame.getWin();
        this.doc = this.frame.getDoc().get();


        this.$el.attr('contenteditable', true);
        this.$el.on('paste', this._paste.bind(this));
        this.$el.on('keydown', this._keydown.bind(this));
        this.$el.on('keyup', this._keyup.bind(this));
    },

    // private
    _keydown: function(e)
    {
        var key = e.which;
        var isCtrl = (e.ctrlKey || e.metaKey);

        // bold
        if (isCtrl && key === 66)
        {
            e.preventDefault();
            this.doc.execCommand('bold', false, null);
        }
        // italic
        else if (isCtrl && key === 73)
        {
            e.preventDefault();
            this.doc.execCommand('italic', false, null);
        }
    },
    _keyup: function(e)
    {
        this._setSource(this.$el.html());
        this.app.broadcast('changed', this);
    },
    _paste: function(e)
    {
        e.preventDefault();
        var html = e.clipboardData.getData('text/html') || e.clipboardData.getData('text/plain');
        html = this._stripTags(html, '<a><b><strong><i><em><del>');
        html = this._stripStyle(html);
        html = this._buildLinks(html);

        this._insertHtml(html);
        this._setSource(html);
    },
    _insertHtml: function(html)
    {
        if (!this.win.getSelection) return;

        var lastNode;
        var sel = this.win.getSelection();
        if (sel.getRangeAt && sel.rangeCount)
        {
            var range = sel.getRangeAt(0);
            range.deleteContents();

            var el = document.createElement("div");
            el.innerHTML = html;

            var frag = document.createDocumentFragment(), node, lastNode;
            while ((node = el.firstChild))
            {
                lastNode = frag.appendChild(node);
            }

            range.insertNode(frag);

            if (lastNode)
            {
                range = range.cloneRange();
                range.setStartAfter(lastNode);
                range.collapse(true);
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }
    },
    _setSource: function(html)
    {
        var instance = this.$el.closest('[data-element-id]').dataget('instance');
        var $source = instance.getSource();

        var $wrapper = $RE.dom('<div>');
        $wrapper.html(html);
        $wrapper.find('a').each(function(node)
        {
            var $node = $RE.dom(node);
            var $relink = $RE.dom('<re-link>');

            $relink.html($node.html());
            $relink.attr({
                'href': $node.attr('href'),
                'color': node.style.color,
                'font-size': node.style.fontSize
            });

            $node.replaceWith($relink);

        });

        $source.html($wrapper.html());
    },
    _buildLinks: function(html)
    {
        var css = {
            'text-decoration': 'underline',
            'font-family': this.opts.styles.text['font-family'],
            'font-size': this.opts.styles.text['font-size'],
            'font-weight':  'normal',
            'line-height': this.opts.styles.text['line-height'],
            'color': this.opts.styles.link['color']
        };

        var $wrapper = $RE.dom('<div>');
        $wrapper.html(html);
        $wrapper.find('a').attr('target', '_blank').css(css);

        return $wrapper.html();
    },
    _stripStyle: function(html)
    {
        var $wrapper = $RE.dom('<div>');
        $wrapper.html(html);
        $wrapper.find('[style]').removeAttr('style');

        return $wrapper.html();
    },
    _stripTags: function(html, allowed)
    {
        allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;

        return html.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1)
        {
            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
        });
    }
});
$RE.add('service', 'upload', {
    init: function(app)
    {
        this.app = app;

    },
    push: function($el, callbackComplete, callbackRemove, width)
    {
        var $el = $RE.dom($el);

        this.uploadClass = $RE.create('upload.class', this.app, $el, callbackComplete, callbackRemove, width);
        this.$upload = this.uploadClass.buildImage();
    },
    build: function($el, callbackComplete, callbackRemove, width)
    {
        var $el = $RE.dom($el);

        this.uploadClass = $RE.create('upload.class', this.app, $el, callbackComplete, callbackRemove, width);
        this.$upload = ($el.get().tagName === 'INPUT') ? this.uploadClass.buildInput() : this.uploadClass.buildBox();
    },
    set: function($img, remove)
    {
        this.$upload.html('');
        this.$upload.append($img);

        if (remove !== false)
        {
            this.uploadClass.buildRemove();
        }
    }
});
$RE.add('class', 'upload.class', {
    init: function(app, $el, callbackComplete, callbackRemove, width)
    {
        this.app = app;
        this.opts = app.opts;
        this.$el = $el;
        this.width = width;
        this.completeCallback = callbackComplete;
        this.removeCallback = callbackRemove;

    },
    buildInput: function()
    {
        this.box = false;
        this.prefix = '';

        this.$uploadbox = $RE.dom('<div class="re-upload-item" />');
        if (this.width)
        {
            this.$uploadbox.width(this.width);
        }

        this.$el.hide();
        this.$el.after(this.$uploadbox);

        this._buildEvents();

        return this.$uploadbox;
    },
    buildBox: function()
    {
        this.box = true;
        this.prefix = 'box-';

        this.$uploadbox = this.$el;
        this.$uploadbox.attr('ondragstart', 'return false;');

        this.$uploadbox.off('.remail-upload');

        // events
        this.$uploadbox.on('drop.remail-upload', this._onDropBox.bind(this));
        this.$uploadbox.on('dragover.remail-upload', this._onDragOver.bind(this));
        this.$uploadbox.on('dragleave.remail-upload', this._onDragLeave.bind(this));

        return this.$uploadbox;
    },
    buildImage: function()
    {
        this.image = true;
        this.prefix = 'box-';

        this.$uploadbox = this.$el;
        this.$uploadbox.attr('ondragstart', 'return false;');

        this.$uploadbox.off('.remail-upload');

        // events
        this.$uploadbox.on('drop.remail-upload', this._onDropBox.bind(this));
        this.$uploadbox.on('dragover.remail-upload', this._onDragOver.bind(this));
        this.$uploadbox.on('dragleave.remail-upload', this._onDragLeave.bind(this));

        return this.$uploadbox;
    },
    buildRemove: function()
    {
        var $remove = $RE.dom('<span>');
        $remove.addClass('re-upload-remove');
        $remove.on('click', function(e)
        {
            e.preventDefault();

            if (typeof this.removeCallback === 'function')
            {
                this.removeCallback();
            }

            this.$uploadbox.html('');
            this.app.broadcast('upload.remove');

        }.bind(this));

        this.$uploadbox.append($remove);
    },

    // private
    _buildEvents: function()
    {
        this.$el.off('.remail-upload');
        this.$uploadbox.off('.remail-upload');

        this.$el.on('change.remail-upload', this._onChange.bind(this));
        this.$uploadbox.on('click.remail-upload', this._onClick.bind(this));
        this.$uploadbox.on('drop.remail-upload', this._onDrop.bind(this));
        this.$uploadbox.on('dragover.remail-upload', this._onDragOver.bind(this));
        this.$uploadbox.on('dragleave.remail-upload', this._onDragLeave.bind(this));
    },
    _onClick: function(e)
    {
        e.preventDefault();

        var $target = $RE.dom(e.target);
        if (!$target.hasClass('re-upload-remove'))
        {
            this.$el.click();
        }
    },
    _onChange: function(e)
    {
        this._send(e, this.$el.get().files);
    },
    _onDrop: function(e)
    {
        e.preventDefault();

        this._clear();
        this._send(e);
    },
    _onDragOver: function(e)
    {
        e.preventDefault();
        this._setStatusHover();

        return false;
    },
    _onDragLeave: function(e)
    {
        e.preventDefault();
        this._removeStatusHover();

        return false;
    },
    _onDropBox: function(e)
    {
        e.preventDefault();

        this._clear();
        this._send(e);
    },
    _send: function(e, files)
    {
        e = e.originalEvent || e;

        files = (files) ? files : e.dataTransfer.files;

        var data = new FormData();
        var name = 'file';

        data.append(name, files[0]);

        var stop = this.app.broadcast('upload.start', e, data, files);
        if (stop !== false)
        {
            this._sendData(data, files, e);
        }
    },
    _sendData: function(data, files, e)
    {
        $RE.ajax.post({
            url: this.opts.upload,
            data: data,
            before: function(xhr)
            {
                return this.app.broadcast('upload.beforeSend', xhr);

            }.bind(this),
            success: function(response)
            {
                this._complete(response, e);
            }.bind(this)
        });
    },
    _complete: function(response, e)
    {
        this._clear();

        if (response && response.error)
        {
            this.app.broadcast('upload.error', response);
        }
        else
        {
            response.$el = this.$el;

            if (typeof this.completeCallback === 'function')
            {
                this.completeCallback(response);

                if (!this.image)
                {
                    // img
                    var $img = $RE.dom('<img>');
                    $img.attr('src', response.url);

                    this.$uploadbox.html('');
                    this.$uploadbox.append($img);

                    if (this.removeCallback !== false)
                    {
                        this.buildRemove();
                    }
                }
            }

            this.app.broadcast('upload.complete', response);

            setTimeout(this._clear.bind(this), 500);
        }
    },
    _removeStatusHover: function()
    {
        this.$uploadbox.removeClass('is-re-upload-' + this.prefix + 'hover');
    },
    _setStatusHover: function()
    {
        this.$uploadbox.addClass('is-re-upload-' + this.prefix + 'hover');
    },
    _clear: function()
    {
        var classes = ['hover'];
        for (var i = 0; i < classes.length; i++)
        {
            this.$uploadbox.removeClass('is-re-upload-' + this.prefix + classes[i]);
        }

        this.$uploadbox.removeAttr('ondragstart');
    }
});
$RE.add('service', 'template', {
    init: function(app)
    {
        this.app = app;

        // local
        this.snapshot = false;
    },

    // public
    build: function(code)
    {
        this._createTemplate();
        this._createSource(code);
        this._render();
    },
    getInitialCode: function()
    {
       return this.opts.doctype + '\n' + '<html></html>';
    },
    getCode: function()
    {
        return this.opts.doctype + '\n' + this.$template.html();
    },
    getSource: function()
    {
        return this.$source.html();
    },
    getSourceBody: function()
    {
        return this.$source.find('re-body');
    },
    revertSnapshot: function()
    {
        if (this.snapshot !== false)
        {
            this.frame.setTemplate(this.snapshot);
            this.snapshot = this.$source.html();
        }
    },
    createSnapshot: function()
    {
        this.snapshot = this.$source.html();
    },
    create: function(tag, props)
    {
        if (typeof props === 'undefined') props = {};
        if (typeof props.$source === 'undefined') props.$source = $RE.dom('<re-' + tag +'>');

        // create
        return $RE.create('template.' + tag, this.app, props, tag);
    },
    parse: function($source)
    {
        var $html = $RE.dom('<div>');

        this._parse($source, $html, true);

        return $html.children().first();
    },

    // private
    _render: function()
    {
        var $html = this.$source.children().first();
        var $nodes = $html.children();

        var $el = this.create('html', { $source: $html }, 'html');
        this.$template.replaceWith($el);

        // parse
        this._parse($nodes, $el);

        // build default styles
        this._buildStyleDefault($el);
    },
    _parse: function($nodes, $parent)
    {
        var self = this;

        $nodes.each(function(node)
        {
            var $node = $RE.dom(node);
            var tag = node.tagName.toLowerCase().replace('re-', '');
            var $el, $children;

            if (self.opts.templateTags.indexOf(tag) !== -1)
            {
                $el = self.create(tag, { $source: $node });

                // parse blocks (not elements)
                if (self.opts.templateNested.indexOf(tag) !== -1)
                {
                    self._parse($node.children(), $el.getContainer());
                }
            }
            else
            {
                $el = $node;
            }

            $parent.append($el);

        });
    },
    _buildStyleDefault: function($html)
    {
        var $default = $RE.dom('<style>');
        $default.attr('type', 'text/css');
        $default.html(this.opts.templateStyles);

        var $head = $html.find('head');
        var $style = $head.find('style');

        if ($style.length !== 0)
        {
            $style.first().before($default);
        }
        else
        {
            $head.append($default);
        }

        // mso styles
        $head.append(this.opts.templateMsoStyles);
    },
    _createTemplate: function()
    {
        this.$template = this.frame.getDoc().find('html');
    },
    _createSource: function(code)
    {
        this.$source = $RE.dom('<div>');
        this.$source.html(code);
    }

});
$RE.add('class', 'template.html', {
    extends: ['dom', 'template'],
    build: function()
    {
        // attrs
        var attrs = {
            'xmlns': 'http://www.w3.org/1999/xhtml'
        };

        // create
        this.parse('<html>');
        this.attr(attrs);

        // set container
        this.setContainer(this);
    }
});
$RE.add('class', 'template.head', {
    extends: ['dom', 'template'],
    build: function()
    {
        // create
        this.parse('<head>');

        // append meta
        this.append('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        this.append('<meta name="viewport" content="width=device-width, initial-scale=1.0"/>');

        // set container
        this.setContainer(this);
    }
});
$RE.add('class', 'template.title', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();

        // create
        this.parse('<title>');
        this.html($source.html());

        // set container
        this.setContainer(this);
    }
});
$RE.add('class', 'template.font', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();

        // attrs
        var attrs = {
            'href': $source.attr('href'),
            'rel': 'stylesheet'
        };

        // create
        this.parse('<link>');
        this.attr(attrs);

        // set container
        this.setContainer(this);
    }
});
$RE.add('class', 'template.style', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();

        // attrs
        var attrs = {
            'type': 'text/css'
        };

        // create
        this.parse('<style>');
        this.attr(attrs);
        this.html($source.html());

        // set container
        this.setContainer(this);
    }
});
$RE.add('class', 'template.body', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();

        // css
        var css = {
            'font-family': this.opts.styles.text['font-family'],
            'margin': 0,
            'padding': 0
        };

        // create
        this.parse('<body>');
        this.css(css);

        // inject
        this.injectAttr(this, $source);

        // set containet
        this.setContainer(this);
    }
});
$RE.add('class', 'template.container', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();
        var width = ($source.attr('width')) ? $source.attr('width') : this.opts.width;

        // props
        var props = {
            attrs: { 'width': this._normalizeWidthAttr(width) },
            css: { 'width': width }
        };

        // table
        this.parse(this.createTable(props));
        this.addClass('container');

        // row
        var $containerRow = $RE.dom('<tr>');

        // cell
        var $containerCell = $RE.dom('<td>');
        $containerCell.attr({
            'align': 'center',
            'valign': 'top'
        });
        $containerCell.css({
            'vertical-align': 'top'
        });

        // inject
        this.injectAttr($containerCell, $source, ['width']);

        // append
        $containerRow.append($containerCell);
        this.append($containerRow);

        // set container
        this.setContainer($containerCell);
    }
});
$RE.add('class', 'template.header', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();
        var width = ($source.attr('width')) ? $source.attr('width') : this.opts.width;
        var align = ($source.attr('align')) ? $source.attr('align') : this.opts.align;

        // props
        var props = {
            attrs: { 'width': this._normalizeWidthAttr(width) },
            css: { 'width': width }
        };

        // table
        this.parse(this.createTable(props));
        this.addClass('container');
        this.addClass('header');

        // row
        var $headerRow = $RE.dom('<tr>');

        // td
        var $headerCell = $RE.dom('<td>');
        $headerCell.attr({
            'valign': 'top',
            'align': align
        });
        $headerCell.css({
            'vertical-align': 'top'
        });

        // inject
        this.injectAttr($headerCell, $source, ['width']);

        // append
        $headerRow.append($headerCell);
        this.append($headerRow);

        // container
        this.setContainer($headerCell);
    }
});
$RE.add('class', 'template.main', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();

        // props
        var props = {
            attrs: { 'width': '100%' },
            css: { 'width': '100%' }
        };

        // table
        this.parse(this.createTable(props));
        this.addClass('main');

        // row
        var $mainRow = $RE.dom('<tr>');

        // td
        var $mainCell = $RE.dom('<td>');
        $mainCell.attr({
            'align': 'center',
            'valign': 'top'
        });
        $mainCell.css({
            'vertical-align': 'top'
        });

        // inject
        this.injectAttr($mainCell, $source);

        // append
        $mainRow.append($mainCell);
        this.append($mainRow);

        // container
        this.setContainer($mainCell);
    }
});
$RE.add('class', 'template.footer', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();
        var width = ($source.attr('width')) ? $source.attr('width') : this.opts.width;
        var align = ($source.attr('align')) ? $source.attr('align') : this.opts.align;

        // props
        var props = {
            attrs: {
                'width': this._normalizeWidthAttr(width)
            },
            css: {
                'width': width
            }
        };

        // table
        this.parse(this.createTable(props));
        this.addClass('container');
        this.addClass('footer');

        // row
        var $footerRow = $RE.dom('<tr>');

       // td
        var $footerCell = $RE.dom('<td>');
        $footerCell.attr({
            'valign': 'top',
            'align': align
        });
        $footerCell.css({
            'vertical-align': 'top'
        });

        // inject
        this.injectAttr($footerCell, $source, ['width']);

        // append
        $footerRow.append($footerCell);
        this.append($footerRow);

        // container
        this.setContainer($footerCell);
    }
});
$RE.add('class', 'template.card', {
    extends: ['dom', 'template'],
    tools: {
        'Text': {
            'text-color': {
                tool: 'color',
                label: 'Color',
                prop: 'color',
                getter: 'getToolTextColor',
                setter: 'setToolTextColor'
            },
            'text-size': {
                tool: 'size',
                label: 'Size',
                prop: 'font-size',
                getter: 'getToolTextSize',
                setter: 'setToolTextSize'
            }
        },
        'Link': {
            'link-color': {
                tool: 'color',
                label: 'Color',
                prop: 'color',
                getter: 'getToolLinkColor',
                setter: 'setToolLinkColor'
            }
        },
        'Padding': {
            'padding': {
                tool: 'size',
                label: 'Size',
                prop: 'padding',
                getter: 'getPadding',
                setter: 'setPadding'
            }
        },
        'Border': {
            'border-size': {
                tool: 'size',
                label: 'Width',
                prop: 'border-width',
                getter: 'getBorderSize',
                setter: 'setBorderSize'
            },
            'border-color': {
                tool: 'color',
                label: 'Color',
                prop: 'border-color',
                getter: 'getBorderColor',
                setter: 'setBorderColor'
            },
            'border-radius': {
                tool: 'size',
                label: 'Radius',
                prop: 'border-radius',
                getter: 'getBorderRadius',
                setter: 'setBorderRadius'
            }
        },
        'Background': {
            'card-backcolor': {
                tool: 'color',
                label: 'Color',
                prop: 'background-color',
                getter: 'getToolBackgroundColor',
                setter: 'setToolBackgroundColor'
            },
            'card-backimage': {
                tool: 'upload-back',
                label: 'Image',
                prop: 'background-image',
                getter: 'getToolBackgroundImage',
                setter: 'setToolBackgroundImage'
            }
        }
    },
    build: function()
    {
        // source
        this.$source = this.getSource();
        var width = (this.$source.attr('width')) ? this.$source.attr('width') : this.opts.width;

        // props
        var props = {
            attrs: { 'width': this._normalizeWidthAttr(width) },
            css: { 'width': width }
        };

        // table
        this.parse(this.createTable(props));
        this.addClass('container');
        this.addClass('card');
        this.addClass('re-card');

        // row
        var $cardRow = $RE.dom('<tr>');

        // td
        this.$cardCell = $RE.dom('<td>');
        this.$cardCell.attr({
            'valign': 'top'
        });
        this.$cardCell.css({
            'vertical-align': 'top'
        });

        // inject
        this.injectAttr(this.$cardCell, this.$source, ['width']);

        // append
        $cardRow.append(this.$cardCell);
        this.append($cardRow);

        // container
        this.setContainer(this.$cardCell);
    },

    // getters
    getToolTextColor: function()
    {
        var $elms = this.$cardCell.find('[data-element-type="text"], [data-element-type="heading"]');
        $elms.each(function(node)
        {
            var $node = $RE.dom(node);
            var instance = $node.dataget('instance');

            instance.saveColor();

        }.bind(this))

        return this.opts.styles.text['color'];
    },
    getToolTextSize: function()
    {
        return this.opts.styles.text['font-size'];
    },
    getToolLinkColor: function()
    {
        var $elms = this.$cardCell.find('[data-element-type="link"]');
        $elms.each(function(node)
        {
            var $node = $RE.dom(node);
            var instance = $node.dataget('instance');

            instance.saveColor();

        }.bind(this));

        return this.opts.styles.link['color'];
    },
    getToolBackgroundColor: function()
    {
        var value = this.$source.attr('background-color');

        return (value == null) ? 'none' : this.utils.getHexValue(value);
    },
    getToolBackgroundImage: function()
    {
        return {
            image: this.$source.attr('background-image'),
            size: this.$source.attr('background-size')
        };
    },
    getBorderSize: function()
    {
        return this.$cardCell.css('border-width');
    },
    getBorderColor: function()
    {
        return this.utils.getHexValue(this.$cardCell.css('border-color'));
    },
    getBorderRadius: function()
    {
        return this.$cardCell.css('border-radius');
    },
    getPadding: function()
    {
        return this.$cardCell.css('padding');
    },

    // setters
    setToolTextColor: function(value, reset)
    {
        this.opts.styles.text['color'] = value;

        var $elms = this.$cardCell.find('[data-element-type="text"], [data-element-type="heading"]');
        $elms.each(function(node)
        {
            var $node = $RE.dom(node);
            var instance = $node.dataget('instance');

            instance.setColor(value, reset);

        }.bind(this));

    },
    setToolTextSize: function(value)
    {
        var lineHeight = (parseInt(value) * this.opts.textLineHeight) + 'px';

        this.opts.styles.text['font-size'] = value;
        this.opts.styles.text['line-height'] = lineHeight;

        var $elms = this.$cardCell.find('[data-element-type="text"], [data-element-type="link"]');
        $elms.each(function(node)
        {
            var $node = $RE.dom(node);
            var instance = $node.dataget('instance');

            instance.setSize(value, lineHeight);

        }.bind(this));
    },
    setToolLinkColor: function(value, reset)
    {
        this.opts.styles.link['color'] = value;

        var $elms = this.$cardCell.find('[data-element-type="link"]');
        $elms.each(function(node)
        {
            var $node = $RE.dom(node);
            var instance = $node.dataget('instance');

            instance.setColor(value, reset);

        }.bind(this));
    },
    setToolBackgroundColor: function(value)
    {
        this._setBackgroundColor(value, this.$cardCell, this.$source);
    },
    setToolBackgroundImage: function(url, size)
    {
        if (url === false && size === false)
        {
            // remove
            this.removeAttr('background');
            this.css({
                'background-image': '',
                'background-position': '',
                'background-size': ''
            });

            this.$source.removeAttr('background-image');
            this.$source.removeAttr('background-size');

            return;
        }
        else if (url === false)
        {
            // set size
            this.css('background-size', size);
            this.$source.attr('background-size', size);

            return;
        }

        this.attr('background', url);
        this.css({
            'background-image': 'url(' + url + ')',
            'background-position': 'top center',
            'background-size': size
        });

        this.$source.attr({
            'background-image': url,
            'background-size': size
        });
    },
    setBorderSize: function(value)
    {
        var color = this.$cardCell.css('border-color');
        var style = 'solid';
        var border = value + ' ' + style + ' ' + this.utils.getHexValue(color);

        this.$cardCell.css('border', border);
        this.$source.attr('border', border);
    },
    setBorderColor: function(value)
    {
        var width = this.$cardCell.css('border-width');
        width = (width === '') ? '1px' : width;

        var style = 'solid';
        var border = width + ' ' + style + ' ' + value;

        this.$cardCell.css('border', border);
        this.$source.attr('border', border);
    },
    setBorderRadius: function(value)
    {
        this.$cardCell.css('border-radius', value);
        this.$source.attr('border-radius', value);
    },
    setPadding: function(value)
    {
        this.$cardCell.css('padding', value);
        this.$source.attr('padding', value);
    }
});
$RE.add('class', 'template.block', {
    extends: ['dom', 'template'],
    tools: {
        'Block': {
            'block-align': {
                tool: 'align',
                label: 'Align',
                prop: 'text-align',
                getter: 'getBlockAlign',
                setter: 'setBlockAlign'
            },
            'block-padding': {
                tool: 'size',
                label: 'Padding',
                prop: 'padding',
                getter: 'getBlockPadding',
                setter: 'setBlockPadding'
            },
            'block-background': {
                tool: 'color',
                label: 'Background',
                prop: 'background-color',
                getter: 'getBackgroundColor',
                setter: 'setBackgroundColor'
            },
            'border-size': {
                tool: 'size',
                label: 'Border Width',
                prop: 'border-width',
                getter: 'getBorderSize',
                setter: 'setBorderSize'
            },
            'border-color': {
                tool: 'color',
                label: 'Border Color',
                prop: 'border-color',
                getter: 'getBorderColor',
                setter: 'setBorderColor'
            },
            'border-radius': {
                tool: 'size',
                label: 'Border Radius',
                prop: 'border-radius',
                getter: 'getBorderRadius',
                setter: 'setBorderRadius'
            },
            'column-space': {
                tool: 'size',
                label: 'Column Space',
                prop: 'width',
                getter: 'getColumnSpacerWidth',
                setter: 'setColumnSpacerWidth'
            }
        }
    },
    build: function()
    {
        // source
        this.$source = this.getSource();
        var align = (this.$source.attr('align')) ? this.$source.attr('align') : this.opts.align;

        // props
        var props = {
            attrs: { 'width': '100%' },
            css: { 'width': '100%' }
        };

        // table
        this.parse(this.createTable(props));
        this.addClass('block');
        this.addClass('re-block');

        // row
        var $blockRow = $RE.dom('<tr>');

        // cell
        this.$blockCell = $RE.dom('<td>');
        this.$blockCell.attr({
            'align': align,
            'valign': 'top'
        });
        this.$blockCell.css({
            'vertical-align': 'top'
        });

        if (this.$source.attr('border-radius'))
        {
            this.css('border-collapse', 'separate');
        }

        // mc edit
        if (this.$source.attr('mc-edit'))
        {
            this.$blockCell.attr('mc:edit', this.randomId);
        }

        // inject
        this.injectAttr(this.$blockCell, this.$source, ['align']);

        // append
        $blockRow.append(this.$blockCell);
        this.append($blockRow);

        // set container
        this.setContainer(this.$blockCell);
    },

    // getters
    getBlockAlign: function()
    {
        var align = (this.$source.attr('align')) ? this.$source.attr('align') : this.opts.align;

        return align;
    },
    getBlockPadding: function()
    {
        return this.$blockCell.css('padding');
    },
    getColumnSpacerWidth: function()
    {
         return this.$blockCell.find('[data-element-type="column-spacer"]').attr('width') + 'px';
    },
    getBackgroundColor: function()
    {
        var value = this.$source.attr('background-color');

        return (value == null) ? 'none' : this.utils.getHexValue(value);
    },
    getBorderSize: function()
    {
        return this.$blockCell.css('border-width');
    },
    getBorderColor: function()
    {
        return this.utils.getHexValue(this.$blockCell.css('border-color'));
    },
    getBorderRadius: function()
    {
        return this.$blockCell.css('border-radius');
    },


    // setters
    setBlockAlign: function(value)
    {
        this.$blockCell.attr('align', value);
        this.$source.attr('align', value);

        var $elms = this.$blockCell.find('[data-element-type="column"], [data-element-type="text"], [data-element-type="heading"], [data-element-type="image"], [data-element-type="button-link"]');
        $elms.each(function(node)
        {
            var $node = $RE.dom(node);
            var instance = $node.dataget('instance');

            instance.setAlign(value);

        }.bind(this));
    },
    setBlockPadding: function(value)
    {
         this.$blockCell.css('padding', value);
         this.$source.attr('padding', value);
    },
    setColumnSpacerWidth: function(value)
    {
        var $elms = this.$blockCell.find('[data-element-type="column-spacer"]');
        $elms.each(function(node)
        {
            var $node = $RE.dom(node);
            var instance = $node.dataget('instance');

            instance.setWidth(value);

        }.bind(this));

    },
    setBackgroundColor: function(value)
    {
        this._setBackgroundColor(value, this.$blockCell, this.$source);
    },
    setBorderSize: function(value)
    {
        var color = this.$blockCell.css('border-color');
        var style = 'solid';
        var border = value + ' ' + style + ' ' + this.utils.getHexValue(color);

        this.$blockCell.css('border', border);
        this.$source.attr('border', border);
    },
    setBorderColor: function(value)
    {
        var width = this.$blockCell.css('border-width');
        width = (width === '') ? '1px' : width;

        var style = 'solid';
        var border = width + ' ' + style + ' ' + value;

        this.$blockCell.css('border', border);
        this.$source.attr('border', border);
    },
    setBorderRadius: function(value)
    {
        this.css('border-collapse', 'separate');
        this.$blockCell.css('border-radius', value);
        this.$source.attr('border-radius', value);
    }
});
$RE.add('class', 'template.spacer', {
    extends: ['dom', 'template'],
    tools: {
        'Spacer': {
            'spacer-size': {
                tool: 'size',
                label: 'Height',
                prop: 'height',
                getter: 'getHeight',
                setter: 'setHeight'
            }
        }
    },
    build: function()
    {
        // source
        this.$source = this.getSource();
        var height = this.$source.attr('height') || '1px';
        var width = this.$source.attr('width') || '100%';

        // props
        var props = {
            attrs: { 'width': this._normalizeWidthAttr(width) },
            css: { 'width': width }
        };

        // table
        this.parse(this.createTable(props));
        this.addClass('container');

        // row
        var $spacerRow = $RE.dom('<tr>');

        // cell
        this.$spacerCell = $RE.dom('<td>');
        this.$spacerCell.html('&nbsp;');
        this.$spacerCell.css({
            'padding': 0,
            'font-size': height,
            'line-height': height
        });

        // inject
        this.injectAttr(this.$spacerCell, this.$source, ['width']);

        // append
        $spacerRow.append(this.$spacerCell);
        this.append($spacerRow);

        // set container
        this.setContainer(this.$spacerCell);
    },

    // getters
    getHeight: function()
    {
        return this.$source.attr('height');
    },

    // setters
    setHeight: function(value)
    {
        this.$spacerCell.css({
            'font-size': value,
            'line-height': value,
            'height': value
        });

        this.$spacerCell.attr('height', value);
        this.$source.attr('height', value);
    }
});
$RE.add('class', 'template.divider', {
    extends: ['dom', 'template'],
    tools: {
        'Divider': {
            'divider-size': {
                tool: 'size',
                label: 'Height',
                prop: 'height',
                getter: 'getHeight',
                setter: 'setHeight'
            },
            'divider-color': {
                tool: 'color',
                label: 'Color',
                prop: 'background-color',
                getter: 'getBackgroundColor',
                setter: 'setBackgroundColor'
            }
        }
    },
    build: function()
    {
        // source
        this.$source = this.getSource();
        var height = (this.$source.attr('height')) ? this.$source.attr('height') : '1px';
        var width = (this.$source.attr('width')) ? this.$source.attr('width') : '100%';

        // props
        var props = {
            attrs: { 'width': this._normalizeWidthAttr(width) },
            css: { 'width': width }
        };

        // table
        this.parse(this.createTable(props));
        this.addClass('container');

        // row
        var $dividerRow = $RE.dom('<tr>');

        // cell
        this.$dividerCell = $RE.dom('<td>');
        this.$dividerCell.html('&nbsp;');
        this.$dividerCell.attr({
            'valign': 'top'
        });
        this.$dividerCell.css({
            'vertical-align': 'top',
            'padding': 0,
            'font-size': height,
            'line-height': height
        });

        // inject
        this.injectAttr(this.$dividerCell, this.$source, ['width']);

        // append
        $dividerRow.append(this.$dividerCell);
        this.append($dividerRow);

        // set container
        this.setContainer(this.$dividerCell);
    },


    // getters
    getHeight: function()
    {
        return this.$source.attr('height');
    },
    getBackgroundColor: function()
    {
        var value = this.$source.attr('background-color');

        return (value == null) ? 'none' : this.utils.getHexValue(value);
    },

    // setters
    setHeight: function(value)
    {
        this.$dividerCell.css({
            'font-size': value,
            'line-height': value,
            'height': value
        });

        this.$dividerCell.attr('height', value);
        this.$source.attr('height', value);
    },
    setBackgroundColor: function(value)
    {
        this._setBackgroundColor(value, this.$dividerCell, this.$source);
    }
});
$RE.add('class', 'template.grid', {
    extends: ['dom', 'template'],
    build: function()
    {
        // props
        var props = {
            attrs: { 'width': '100%' },
            css: { 'width': '100%' }
        };

        // create
        this.parse(this.createTable(props));

        // set container
        this.setContainer(this);
    }
});
$RE.add('class', 'template.row', {
    extends: ['dom', 'template'],
    build: function()
    {
        this.parse('<tr>');
        this.setContainer(this);
    }
});
$RE.add('class', 'template.column', {
    extends: ['dom', 'template'],
    tools: {
        'Column': {
            'column-align': {
                tool: 'align',
                label: 'Align',
                prop: 'align',
                getter: 'getColumnAlign',
                setter: 'setColumnAlign'
            },
            'column-padding': {
                tool: 'size',
                label: 'Padding',
                prop: 'padding',
                getter: 'getColumnPadding',
                setter: 'setColumnPadding'
            }
        }
    },
    build: function()
    {
        this.$source = this.getSource();
        var align = (this.$source.attr('align')) ? this.$source.attr('align') : this.opts.align;

        // create
        this.parse('<td>');
        this.addClass('mobile');
        this.attr({
            'align': align,
            'valign': 'top'
        });
        this.css({
            'vertical-align': 'top'
        });

        // mc edit
        if (this.$source.attr('mc-edit'))
        {
            this.attr('mc:edit', this.randomId);
        }

        // inject
        this.injectAttr(this, this.$source, ['align']);

        /// set container
        this.setContainer(this);
    },

    // setters
    setAlign: function(value)
    {
        this.attr('align', value);
        this.$source.attr('align', value);
    },
    setColumnAlign: function(value)
    {
        this.setAlign(value);

        var $elms = this.find('[data-element-type="text"], [data-element-type="heading"], [data-element-type="image"], [data-element-type="button-link"]');
        $elms.each(function(node)
        {
            var $node = $RE.dom(node);
            var instance = $node.dataget('instance');

            instance.setAlign(value);

        }.bind(this));
    },
    setColumnPadding: function(value)
    {
        this.css('padding', value);
        this.$source.attr('padding', value);
    },

    // getters
    getColumnAlign: function()
    {
        var align = (this.$source.attr('align')) ? this.$source.attr('align') : 'left';

        return align;
    },
    getColumnPadding: function()
    {
        return this.css('padding');
    }
});
$RE.add('class', 'template.column-spacer', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        this.$source = this.getSource();

        // create
        this.parse('<td>');
        this.addClass('mobile-hidden');
        this.html('&nbsp;');

        // inject
        this.injectAttr(this, this.$source);

        // set container
        this.setContainer(this);
    },

    // setters
    setWidth: function(value)
    {
        this.attr('width', parseInt(value));
        this.css('width', value);

        this.$source.attr('width', value);
    }
});
$RE.add('class', 'template.text', {
    extends: ['dom', 'template'],
    tools: {
        'Text': {
            'text-color': {
                tool: 'color',
                label: 'Color',
                prop: 'color',
                getter: 'getColor',
                setter: 'setColor'
            },
            'text-size': {
                tool: 'size',
                label: 'Font Size',
                prop: 'font-size',
                getter: 'getTextSize',
                setter: 'setTextSize'
            }
        }
    },
    build: function()
    {
        // source
        this.$source = this.getSource();
        var html = this._parseLinks(this.$source.html());
        var lineHeight = this._getTextLineHeight(this.$source);

        // props
        var props = {
            attrs: { 'width': '100%' },
            css: { 'width': '100%' }
        };

        // table
        this.parse(this.createTable(props));

        // row
        var $textRow = $RE.dom('<tr>');

        // cell
        this.$textCell = $RE.dom('<td>');
        this.$textCell.addClass('re-editable');
        this.$textCell.html(html);
        this.$textCell.attr({
            'valign': 'top',
            'mc:edit': this.randomId
        });
        this.$textCell.css({
            'vertical-align': 'top',
            'font-family': this.opts.styles.text['font-family'],
            'font-size': this.opts.styles.text['font-size'],
            'line-height': lineHeight,
            'color': this.opts.styles.text['color']
        });

        // append
        $textRow.append(this.$textCell);
        this.append($textRow);

        // inject
        this.injectAttr(this.$textCell, this.$source);

        // set container
        this.setContainer(this.$textCell);
    },
    saveColor: function()
    {
        this.tmpColor = this.getColor();
    },

    // setters
    setAlign: function(value)
    {
        this.$textCell.attr('align', value);
        this.$textCell.css('text-align', value);
        this.$source.attr('align', value);
    },
    setColor: function(value, reset)
    {
        value = (reset && this.tmpColor) ? this.tmpColor : value;

        this.$source.attr('color', value);
        this.$textCell.css('color', value);
    },
    setSize: function(value, lineHeight)
    {
        this.$source.attr({
            'font-size': value,
            'line-height': lineHeight
        });

        this.$textCell.css({
            'font-size': value,
            'line-height': lineHeight
        });
    },
    setTextSize: function(value)
    {
        var lineHeight = (parseInt(value) * this.opts.textLineHeight) + 'px';

        this.setSize(value, lineHeight);
    },

    // getters
    getColor: function()
    {
        return this.utils.getHexValue(this.$textCell.css('color'));
    },
    getTextSize: function()
    {
        return this.$textCell.css('font-size');
    }
});
$RE.add('class', 'template.heading', {
    extends: ['dom', 'template'],
    tools: {
        'Heading': {
            'heading-color': {
                tool: 'color',
                label: 'Color',
                prop: 'color',
                getter: 'getColor',
                setter: 'setColor'
            },
            'heading-size': {
                tool: 'size',
                label: 'Font Size',
                prop: 'font-size',
                getter: 'getHeadingSize',
                setter: 'setHeadingSize'
            },
            'heading-url': {
                tool: 'url',
                label: 'Url',
                prop: 'href',
                getter: 'getHeadingUrl',
                setter: 'setHeadingUrl'
            }
        }
    },
    build: function()
    {
        // source
        this.$source = this.getSource();
        var type = (this.$source.attr('type')) ? this.$source.attr('type') : 'h1';
        var href = this.$source.attr('href');
        var html = this.$source.html();
        var fontSize = this.$source.attr('font-size') || this.opts.headings[type];

        // props
        var props = {
            attrs: { 'width': '100%' },
            css: { 'width': '100%' }
        };

        // table
        this.parse(this.createTable(props));

        // row
        var $headingRow = $RE.dom('<tr>');

        // cell
        this.$headingCell = $RE.dom('<td>');
        this.$headingCell.attr({
            'valign': 'top',
            'mc:edit': this.randomId
        });
        this.$headingCell.css({
            'vertical-align': 'top',
            'font-family': this.opts.styles.text['font-family'],
            'font-size': fontSize,
            'font-weight': 'bold',
            'line-height': this._getHeadingLineHeight(fontSize) + 'px',
            'color': this.opts.styles.text['color']
        });

        // append cell
        $headingRow.append(this.$headingCell);

        // link
        if (href)
        {
            this.$headingLink = $RE.dom('<a>');
            this.$headingLink.attr({
               'target': '_blank'
            });
            this.$headingLink.css({
               'font-family': this.opts.styles.text['font-family'],
               'font-size': fontSize,
               'font-weight': 'bold',
               'line-height': this._getHeadingLineHeight(fontSize) + 'px',
               'color': this.opts.styles.text['color'],
               'text-decoration': 'none'
            });

            this.$headingLink.addClass('re-editable');
            this.$headingLink.html(html);

            // append
            this.$headingCell.append(this.$headingLink);

            // inject
            this.injectAttr(this.$headingLink, this.$source, ['padding', 'class', 'type']);

            // set container
            this.setContainer(this.$headingLink);
        }
        else
        {
            this.$headingCell.addClass('re-editable');
            this.$headingCell.html(html);

            // set container
            this.setContainer(this.$headingCell);
        }

        // inject
        this.injectAttr(this.$headingCell, this.$source, ['href', 'type', 'text-decoration']);


        // append
        this.append($headingRow);
    },
    saveColor: function()
    {
        this.tmpColor = this.getColor();
    },

    // setters
    setAlign: function(value)
    {
        this.$headingCell.attr('align', value);
        this.$headingCell.css('text-align', value);
        this.$source.attr('align', value);
    },
    setColor: function(value, reset)
    {
        value = (reset && this.tmpColor) ? this.tmpColor : value;

        this.$source.attr('color', value);
        this.$headingCell.css('color', value);

        if (this.$headingLink)
        {
            this.$headingLink.css('color', value);
        }
    },
    setHeadingSize: function(value)
    {
        var lineHeight = this._getHeadingLineHeight(value) + 'px';

        this.$source.attr({
            'font-size': value,
            'line-height': lineHeight
        });

        this.$headingCell.css({
            'font-size': value,
            'line-height': lineHeight
        });

        if (this.$headingLink)
        {
            this.$headingLink.css({
                'font-size': value,
                'line-height': lineHeight
            });
        }
    },
    setHeadingUrl: function(value)
    {
        if (value.trim() === '')
        {
            // remove
            this.$source.removeAttr('href');

            if (this.$headingLink)
            {
                var html = this.$headingLink.html();
                this.$headingLink.remove();
                this.$headingCell.html(html);

                this.$headingLink = false;
            }
        }
        else if (this.$headingLink)
        {
            // set
            this.$headingLink.attr('href', value);
            this.$source.attr('href', value);
        }
        else
        {
            // create
            var html = this.$headingCell.html();
            var fontSize = this.$headingCell.css('font-size');
            var color = this.$headingCell.css('color');

            this.$headingLink = $RE.dom('<a>');
            this.$headingLink.attr({
               'target': '_blank'
            });
            this.$headingLink.css({
               'font-family': this.opts.styles.text['font-family'],
               'font-size': fontSize,
               'font-weight': 'bold',
               'line-height': this._getHeadingLineHeight(fontSize) + 'px',
               'color': color,
               'text-decoration': 'none'
            });

            this.$headingLink.addClass('re-editable');
            this.$headingLink.attr('href', value);
            this.$headingLink.html(html);

            // append
            this.$headingCell.html(this.$headingLink);

            // change source
            this.$source.attr('href', value);

        }
    },

    // getters
    getColor: function()
    {
        return this.utils.getHexValue(this.$headingCell.css('color'));
    },
    getHeadingSize: function()
    {
        return this.$headingCell.css('font-size');
    },
    getHeadingUrl: function()
    {
        return this.$source.attr('href');
    }
});
$RE.add('class', 'template.button', {
    extends: ['dom', 'template'],
    tools: {
        'Button': {
            'button-url': {
                tool: 'url',
                label: 'Url',
                prop: 'href',
                getter: 'getButtonUrl',
                setter: 'setButtonUrl'
            },
            'button-color': {
                tool: 'color',
                label: 'Text',
                prop: 'color',
                getter: 'getButtonColor',
                setter: 'setButtonColor'
            },
            'button-background': {
                tool: 'color',
                label: 'Back',
                prop: 'background-color',
                getter: 'getButtonBackgroundColor',
                setter: 'setButtonBackgroundColor'
            }
        }
    },
    build: function()
    {
        // source
        this.$source = this.getSource();
        var html = this.$source.html();
        var borderColor = this.$source.attr('background-color') || this.opts.styles.button['background-color'];
        var bgColor = this.$source.attr('background-color') || this.opts.styles.button['background-color'];

        // props
        var props = {
            css: { 'width': 'auto' }
        };

        // table
        this.parse(this.createTable(props));

        // row
        var $buttonRow = $RE.dom('<tr>');

        // cell
        this.$buttonCell = $RE.dom('<td>');
        this.$buttonCell.attr({
            'valign': 'top',
            'bgcolor': bgColor,
            'align': 'center'
        });
        this.$buttonCell.css({
            'vertical-align': 'top',
            'text-align': 'center',
            'font-family': this.opts.styles.text['font-family'],
            'font-size': this.opts.styles.button['font-size'],
            'font-weight': this.opts.styles.button['font-weight'],
            'color': this.opts.styles.button['color'],
            'background-color': bgColor,
            'border-radius': '4px'
        });

        // link
        this.$buttonLink = $RE.dom('<a>');
        this.$buttonLink.html(html);
        this.$buttonLink.addClass('re-editable');
        this.$buttonLink.attr({
            'target': '_blank',
            'mc:edit': this.randomId
        });
        this.$buttonLink.css({
            'display': 'inline-block',
            'box-sizing': 'border-box',
            'cursor': 'pointer',
            'text-decoration': 'none',
            'margin': 0,
            'padding': '12px 20px',
            'border': '1px solid ' + borderColor,
            'font-family': this.opts.styles.text['font-family'],
            'font-size': this.opts.styles.button['font-size'],
            'font-weight': this.opts.styles.button['font-weight'],
            'color': this.opts.styles.button['color'],
            'background-color': bgColor,
            'border-radius': '4px'

        });

        // inject
        this.injectAttr(this.$buttonLink, this.$source, ['class']);
        this.injectAttr(this.$buttonCell, this.$source, ['border', 'padding', 'href']);

        // append
        this.$buttonCell.append(this.$buttonLink);
        $buttonRow.append(this.$buttonCell);
        this.append($buttonRow);

        // container
        this.setContainer(this.$buttonCell);
    },

    // getters
    getButtonUrl: function()
    {
        return this.$source.attr('href');
    },
    getButtonColor: function()
    {
        return this.utils.getHexValue(this.$buttonLink.css('color'));
    },
    getButtonBackgroundColor: function()
    {
        return this.utils.getHexValue(this.$buttonLink.css('background-color'));
    },

    // setters
    setButtonUrl: function(value)
    {
        this.$buttonLink.attr('href', value);
        this.$source.attr('href', value);
    },
    setButtonColor: function(value)
    {
        this.$buttonLink.css('color', value);
        this.$buttonCell.css('color', value);
        this.$source.attr('color', value);
    },
    setButtonBackgroundColor: function(value)
    {
        this.$buttonLink.css('background-color', value);
        this.$buttonLink.css('border-color', value);
        this.$buttonCell.css('background-color', value);
        this.$source.attr('background-color', value);
    }
});
$RE.add('class', 'template.button-link', {
    extends: ['dom', 'template'],
    tools: {
        'Button Link': {
            'button-url': {
                tool: 'url',
                label: 'Url',
                prop: 'href',
                getter: 'getButtonUrl',
                setter: 'setButtonUrl'
            }
        }
    },
    build: function()
    {
        // source
        this.$source = this.getSource();
        var html = this.$source.html();

        // props
        var props = {
            attrs: { 'width': '100%' },
            css: { 'width': '100%' }
        };

        // table
        this.parse(this.createTable(props));

        // row
        var $buttonRow = $RE.dom('<tr>');

        // cell
        this.$buttonCell = $RE.dom('<td>');
        this.$buttonCell.attr({
            'valign': 'top'
        });
        this.$buttonCell.css({
            'vertical-align': 'top'
        });

        // link
        this.$buttonLink = $RE.dom('<a>');
        this.$buttonLink.html(html);
        this.$buttonLink.addClass('re-editable');
        this.$buttonLink.attr({
            'target': '_blank',
            'mc:edit': this.randomId
        });
        this.$buttonLink.css({
            'cursor': 'pointer',
            'text-decoration': 'underline',
            'font-family': this.opts.styles.text['font-family'],
            'font-size': '20px',
            'font-weight': 'normal',
            'line-height': '30px',
            'color': this.opts.styles.link['color']
        });

        // inject
        this.injectAttr(this.$buttonLink, this.$source, ['align', 'padding', 'class']);
        this.injectAttr(this.$buttonCell, this.$source, ['text-decoration', 'href']);

        // append
        this.$buttonCell.append(this.$buttonLink);
        $buttonRow.append(this.$buttonCell);
        this.append($buttonRow);

        // container
        this.setContainer(this.$buttonCell);
    },


    // getters
    getButtonUrl: function()
    {
        return this.$source.attr('href');
    },

    // setters
    setAlign: function(value)
    {
        this.$buttonCell.attr('align', value);
        this.$buttonCell.css('text-align', value);
        this.$source.attr('align', value);
    },
    setButtonUrl: function(value)
    {
        this.$buttonLink.attr('href', value);
        this.$source.attr('href', value);
    }
});

$RE.add('class', 'template.button-app', {
    extends: ['dom', 'template'],
    tools: {
        'Button App': {
            'button-url': {
                tool: 'url',
                label: 'Url',
                prop: 'href',
                getter: 'getButtonUrl',
                setter: 'setButtonUrl'
            }
        }
    },
    build: function()
    {
        // source
        this.$source = this.getSource();
        var html = this.$source.html();
        var href = this.$source.attr('href');

        // link
        this.parse('<a>');
        this.html(html);
        this.attr({
            'target': '_blank',
            'href': href
        });
        this.css({
            'cursor': 'pointer',
            'text-decoration': 'none',
            'font-size': '0px',
            'line-height': '100%'
        });

        // image
        var $buttonImage = $RE.dom('<img>');
        $buttonImage.attr({
            'border': 0,
            'alt': html
        });


        // inject
        this.injectAttr(this, this.$source, ['width', 'src']);
        this.injectAttr($buttonImage, this.$source, ['href']);

        // append
        this.append($buttonImage);

        // container
        this.setContainer(this);
    },


    // getters
    getButtonUrl: function()
    {
        return this.$source.attr('href');
    },

    // setters
    setButtonUrl: function(value)
    {
        this.attr('href', value);
        this.$source.attr('href', value);
    }
});

$RE.add('class', 'template.image', {
    extends: ['dom', 'template'],
    tools: {
        'Image': {
            'image': {
                tool: 'upload',
                label: false,
                prop: 'background-image',
                getter: 'getImage',
                setter: 'setImage'
            },
            'image-url': {
                tool: 'url',
                label: 'Url',
                prop: 'href',
                getter: 'getImageUrl',
                setter: 'setImageUrl'
            }
        }
    },
    build: function()
    {
        // source
        this.$source = this.getSource();
        var href = this.$source.attr('href');
        var html = this.$source.html();
        var width = (this.$source.attr('width')) ? this.$source.attr('width') : '100%';
        var src = this.$source.attr('src') || '';
        var borderRadius = this.$source.attr('border-radius');

        // props
        var props = {
            attrs: { 'width': '100%' },
            css: { 'width': '100%' }
        };

        // table
        this.parse(this.createTable(props));

        // row
        var $imageRow = $RE.dom('<tr>');

        // cell
        this.$imageCell = $RE.dom('<td>');
        this.$imageCell.attr({
            'valign': 'middle',
            'mc:edit': this.randomId
        });
        this.$imageCell.css({
            'vertical-align': 'middle'
        });

        // image
        this.$image = this.createImage();
        this.$image.attr({
            'alt': html,
            'width': this._normalizeWidthAttr(width),
            'src': src
        });

        if (borderRadius)
        {
            this.$image.css({
                'border-radius': borderRadius
            });
        }

        // append cell
        $imageRow.append(this.$imageCell);

        // link
        if (href)
        {
            this.$imageLink = $RE.dom('<a>');
            this.$imageLink.attr({
               'target': '_blank',
               'href': href
            });
            this.$imageLink.css({
               'cursor': 'pointer',
               'font-size': '0px',
               'line-height': '100%',
               'text-decoration': 'none'
            });

            // append
            this.$imageLink.append(this.$image);
            this.$imageCell.append(this.$imageLink);
        }
        else
        {
            // append image
            this.$imageCell.append(this.$image);
        }

        // inject cell
        this.injectAttr(this.$imageCell, this.$source, ['href', 'src', 'width', 'border-radius']);

        // append
        this.append($imageRow);

        // set container
        this.setContainer(this.$imageCell);
    },

    // setters
    setAlign: function(value)
    {
        this.$imageCell.attr('align', value);
        this.$imageCell.css('text-align', value);
        this.$source.attr('align', value);
    },
    setImage: function(value)
    {
        this.$image.attr('src', value);
        this.$source.attr('src', value);
    },
    setImageUrl: function(value)
    {
        if (value.trim() === '')
        {
            // remove
            this.$source.removeAttr('href');

            if (this.$imageLink)
            {
                this.$imageLink.after(this.$image);
                this.$imageLink.remove();
                this.$imageLink = false;
            }
        }
        else if (this.$imageLink)
        {
            // set
            this.$imageLink.attr('href', value);
            this.$source.attr('href', value);
        }
        else
        {
            // create
            this.$imageLink = $RE.dom('<a>');
            this.$imageLink.attr({
               'target': '_blank',
               'href': value
            });
            this.$imageLink.css({
               'cursor': 'pointer',
               'font-size': '0px',
               'line-height': '100%',
               'text-decoration': 'none'
            });

            // append
            this.$imageLink.append(this.$image);
            this.$imageCell.html(this.$imageLink);

            // change source
            this.$source.attr('href', value);
        }

    },

    // getters
    getImage: function()
    {
        return this.$source.attr('src');
    },
    getImageUrl: function()
    {
        return this.$source.attr('href');
    }
});
$RE.add('class', 'template.link', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        this.$source = this.getSource();

        // css
        var css = {
            'text-decoration': 'underline',
            'font-family': this.opts.styles.text['font-family'],
            'font-size': this.opts.styles.text['font-size'],
            'font-weight':  'normal',
            'line-height': this.opts.styles.text['line-height'],
            'color': this.opts.styles.link['color']
        };

        // attrs
        var attrs = {
            'target': '_blank'
        };

        // create
        this.parse('<a>');
        this.css(css);
        this.attr(attrs);
        this.html(this.$source.html());

        // inject
        this.injectAttr(this, this.$source);

        // set container
        this.setContainer(this);
    },
    saveColor: function()
    {
        this.tmpColor = this.utils.getHexValue(this.css('color'));
    },

    // setters
    setColor: function(value, reset)
    {
        value = (reset && this.tmpColor) ? this.tmpColor : value;

        this.$source.attr('color', value);
        this.css('color', value);
    },
    setSize: function(value, lineHeight)
    {
        this.$source.attr({
            'font-size': value,
            'line-height': lineHeight
        });

        this.css({
            'font-size': value,
            'line-height': lineHeight
        });
    }
});
$RE.add('class', 'template.social-link', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();
        var href = $source.attr('href');
        var src = $source.attr('src');
        var html = $source.html();

        // size
        var iconSize = '20px';

        // css
        var css = {
            'text-decoration': 'none',
            'font-size': iconSize,
            'line-height': iconSize,
            'display': 'inline-block'
        };

        // attrs
        var attr = {
            'target': '_blank',
            'href': href,
            'mc:edit': this.randomId
        };

        // create
        this.parse('<a>');
        this.attr(attr);
        this.css(css);

        // create image
        var $img = this.createImage();
        $img.attr({
            'width': parseInt(iconSize),
            'src': src,
            'alt': html
        });

        // append
        this.append($img);

        // set container
        this.setContainer(this);
    }
});
$RE.add('class', 'template.menu-link', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();
        var href = $source.attr('href');
        var html = $source.html();

        // css
        var css = {
            'text-decoration': 'none',
            'font-family': this.opts.styles.text['font-family'],
            'font-size': this.opts.styles.text['font-size'],
            'font-weight':  'bold',
            'line-height': this.opts.styles.text['line-height'],
            'color': this.opts.styles.text['color']
        };

        // attr
        var attr = {
            'target': '_blank',
            'href': href
        };

        // create
        this.parse('<a>');
        this.html(html);
        this.css(css);
        this.attr(attr);

        // inject
        this.injectAttr(this, $source);

        // set container
        this.setContainer(this);
    }
});
$RE.add('class', 'template.mobile-spacer', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();

        // props
        var props = {
            attrs: { 'width': '100%' },
            css: { 'width': '100%' }
        };

        // table
        this.parse(this.createTable(props));
        this.addClass('mobile-spacer');

        // row
        var $spacerRow = $RE.dom('<tr>');

        // cell
        var $spacerCell = $RE.dom('<td>');
        $spacerCell.html('&nbsp;');
        $spacerCell.css({
            'valign': 'top'
        });
        $spacerCell.css({
            'vertical-align': 'top',
            'font-size': '0px',
            'line-height': 0
        });

        // inject
        this.injectAttr($spacerCell, $source);

        // append
        $spacerRow.append($spacerCell);
        this.append($spacerRow);

        // container
        this.setContainer($spacerCell);
    }
});
$RE.add('class', 'template.preheader', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();

        // create
        this.parse('<span>');
        this.css({
            'color': 'transparent',
            'display': 'none',
            'height': 0,
            'max-height': 0,
            'max-width': 0,
            'opacity': 0,
            'overflow': 'hidden',
            'mso-hide': 'all',
            'visibility': 'hidden',
            'width': 0
        });
        this.html($source.html());

        // container
        this.setContainer(this);
    }
});
$RE.add('class', 'template.inline-spacer', {
    extends: ['dom', 'template'],
    build: function()
    {
        // source
        var $source = this.getSource();
        var html = $source.html();
        var isEmpty = (html.trim() === '');
        html = (isEmpty) ? '&nbsp;' : html;

        // create
        this.parse('<span>');
        this.css({
            'display': 'inline-block',
            'line-height': 0,
            'font-family': this.opts.styles.text['font-family'],
            'font-size': this.opts.styles.text['font-size'],
            'color': this.opts.styles.text['color']
        });

        if (isEmpty) this.css('width', '0px');
        this.html(html);

        // inject
        this.injectAttr(this, $source);

        // set container
        this.setContainer(this);
    }
});
$RE.add('service', 'tool', {
    init: function(app)
    {
        this.app = app;
    },
    build: function($el)
    {
        this.$el = $el;
        this.$html = $RE.dom('<div>');
        this.$html.addClass('re-tools');


        return (this.$el.hasClass('re-card')) ? this._buildCard() : this._buildBlock();
    },

    // private
    _buildCard: function()
    {
        var instance = this.$el.dataget('instance');
        var toolItems = instance.tools;

        for (var name in toolItems)
        {
            // head
            var tools = toolItems[name];
            var $name = $RE.dom('<h5>');
            $name.html(name);

            // append head
            this.$html.append($name);

            // tools
            for (var key in tools)
            {
                if (tools[key].tool === 'upload-back' && this.opts.upload === false) {
                    continue;
                }

                // container
                var $container = $RE.dom('<div>');
                $container.addClass('re-tool-container');

                // label
                if (tools[key].label !== false)
                {
                    var $label = $RE.dom('<div>');
                    $label.addClass('re-tool-label');
                    $label.html(tools[key].label);
                }

                // input
                var $input = $RE.dom('<div>');
                $input.addClass('re-tool-input');

                // tool
                var $tool = $RE.create('tool.' + tools[key].tool, this.app, tools[key], instance);

                // append
                $input.append($tool);
                if (tools[key].label !== false) $container.append($label);
                $container.append($input);
                this.$html.append($container);
            }
        }

        return this.$html;
    },
    _buildBlock: function()
    {
        var instance = this.$el.dataget('instance');

        // block
        var $name = $RE.dom('<h5>');
        $name.html('Block');

        this.$html.append($name);

        // block tools
        var $slideContainer = $RE.dom('<div>').addClass('re-slide-container re-slide-container-block');
        var blockTools = instance.tools;
        var finalTools = {};
        var $spacer = this.$el.find('[data-element-type="column-spacer"]').first();
        if ($spacer.length === 0)
        {
            for (var name in blockTools)
            {
                finalTools[name] = {};

                for (var key in blockTools[name])
                {
                    if (key !== 'column-space')
                    {
                        finalTools[name][key] = blockTools[name][key];
                    }
                }
            }
        }
        else
        {
            finalTools = blockTools;
        }

        // create block tools
        this._createTool(finalTools, $slideContainer, instance);

        // append block tools
        this.$html.append($slideContainer);

        // block elements
        // elements tools
        var $images = this.$el.find('[data-element-type="image"]');
        var $headings = this.$el.find('[data-element-type="heading"]');
        var $text = this.$el.find('[data-element-type="text"]');
        var $buttons = this.$el.find('[data-element-type="button"]');
        var $buttonsLink = this.$el.find('[data-element-type="button-link"]');
        var $buttonsApp = this.$el.find('[data-element-type="button-app"]');
        var $spacers = this.$el.find('[data-element-type="spacer"]');
        var $dividers = this.$el.find('[data-element-type="divider"]');
        var $columns = this.$el.find('[data-element-type="column"]');

        this._createSlideContainer($images, 'Image');
        this._createSlideContainer($headings, 'Heading');
        this._createSlideContainer($text, 'Text');
        this._createSlideContainer($buttons, 'Button');
        this._createSlideContainer($buttonsLink, 'Button Link');
        this._createSlideContainer($buttonsApp, 'Button App');
        this._createSlideContainer($spacers, 'Spacer');
        this._createSlideContainer($dividers, 'Divider');
        this._createSlideContainer($columns, 'Column');

        return this.$html;
    },
    _createSlideContainer: function($items, name)
    {
        if ($items.length === 0) return;

        var $slideContainer = $RE.dom('<div>');
        $slideContainer.addClass('re-slide-container');

        $items.each(function(node, index)
        {
            var $node = $RE.dom(node);
            var $slideBox = $RE.dom('<div>');
            $slideBox.addClass('re-slide-box');

            var nodeInstance = $node.dataget('instance');
            var nodeTools = nodeInstance.tools;

            this._createTool(nodeTools, $slideBox, nodeInstance);

            var $name = this._createName(name, $items, index);

            $slideBox.prepend($name);
            $slideContainer.append($slideBox);

        }.bind(this));

        this.$html.append($slideContainer);
    },
    _createName: function(name, $items, index)
    {
        var $name = $RE.dom('<h5>');
        var suffix = ($items.length === 1) ? '' : ' ' + (parseInt(index+1));
        $name.html(name + suffix);

        return $name;
    },
    _createTool: function(nodeTools, $targetContainer, instance)
    {
        var type = instance.attr('data-element-type');
        for (var name in nodeTools)
        {
            var tools = nodeTools[name];

            for (var key in tools)
            {
                if (type === 'image' && tools[key].tool === 'upload' && this.opts.upload === false) {
                    continue;
                }

                var $container = $RE.dom('<div>');
                $container.addClass('re-slide-tool-container');

                if (tools[key].label !== false)
                {
                    var $label = $RE.dom('<div>');
                    $label.addClass('re-slide-tool-label');
                    $label.html(tools[key].label);
                }

                var $input = $RE.dom('<div>');
                $input.addClass('re-slide-tool-input');

                var $tool = $RE.create('tool.' + tools[key].tool, this.app, tools[key], instance);

                $input.append($tool);
                if (tools[key].label !== false) $container.append($label);
                $container.append($input);

                $targetContainer.append($container);
            }
        }
    }
});
$RE.add('class', 'tool.align', {
    init: function(app, props, instance)
    {
        this.app = app;
        this.opts = app.opts;
        this.utils = app.utils;
        this.template = app.template;

        // local
        this.instance = instance;
        this.props = props;

        // create
        this._create();
        this._createValue();
        this._createInput();
        this._createEvents();

        return this.$tool;
    },

    // private
    _create: function()
    {
        this.$tool = $RE.dom('<div>');
        this.$tool.addClass('re-tool');
        this.$tool.attr('data-type', this.name);
    },
    _createValue: function()
    {
        this.value = this.instance[this.props.getter]();
    },
    _createInput: function()
    {
        this.$inputContainer = $RE.dom('<div>');
        this.$inputContainer.addClass('re-align-container');

        this.$input = {};

        var align = ['left', 'center', 'right'];
        for (var i = 0; i < align.length; i++)
        {
            this.$input[align[i]] = $RE.dom('<span>');
            this.$input[align[i]].addClass('re-align-span');
            this.$input[align[i]].attr('data-type', align[i]);
            this.$input[align[i]].html(this.opts.icons['align-' + align[i]]);
        }

        for (var key in this.$input)
        {
            if (key === this.value)
            {
                this.$input[key].addClass('is-re-active');
            }

            this.$inputContainer.append(this.$input[key]);
        }

        this.$tool.append(this.$inputContainer);
    },
    _createEvents: function()
    {
        for (var key in this.$input)
        {
            this.$input[key].on('click', this._setAlign.bind(this));
        }
    },
    _setAlign: function(e)
    {
        e.preventDefault();

        this.$inputContainer.find('span').removeClass('is-re-active');

        var $input = $RE.dom(e.target).closest('.re-align-span');
        var value = $input.attr('data-type');

        $input.addClass('is-re-active');

        // setter
        this.instance[this.props.setter](value);

        // adjust
        this.app.broadcast('adjustHeight');
        this.app.broadcast('changed', this, this.instance);
    }
});
$RE.add('class', 'tool.color', {
    init: function(app, props, instance)
    {
        this.app = app;
        this.tool = app.tool;
        this.opts = app.opts;
        this.frame = app.frame;

        // local
        this.instance = instance;
        this.props = props;

        this.colors = {
            gray:   ['#f8f9fa', '#f1f3f5', '#e9ecef', '#dee2e6', '#ced4da', '#adb5bd', '#868e96', '#495057', '#343a40', '#212529'],
            red:    ["#fff5f5", "#ffe3e3", "#ffc9c9", "#ffa8a8", "#ff8787", "#ff6b6b", "#fa5252", "#f03e3e", "#e03131", "#c92a2a"],
            pink:   ["#fff0f6", "#ffdeeb", "#fcc2d7", "#faa2c1", "#f783ac", "#f06595", "#e64980", "#d6336c", "#c2255c", "#a61e4d"],
            grape:  ["#f8f0fc", "#f3d9fa", "#eebefa", "#e599f7", "#da77f2", "#cc5de8", "#be4bdb", "#ae3ec9", "#9c36b5", "#862e9c"],
            violet: ["#f3f0ff", "#e5dbff", "#d0bfff", "#b197fc", "#9775fa", "#845ef7", "#7950f2", "#7048e8", "#6741d9", "#5f3dc4"],
            indigo: ["#edf2ff", "#dbe4ff", "#bac8ff", "#91a7ff", "#748ffc", "#5c7cfa", "#4c6ef5", "#4263eb", "#3b5bdb", "#364fc7"],
            blue:   ["#e7f5ff", "#d0ebff", "#a5d8ff", "#74c0fc", "#4dabf7", "#339af0", "#228be6", "#1c7ed6", "#1971c2", "#1864ab"],
            cyan:   ["#e3fafc", "#c5f6fa", "#99e9f2", "#66d9e8", "#3bc9db", "#22b8cf", "#15aabf", "#1098ad", "#0c8599", "#0b7285"],
            teal:   ["#e6fcf5", "#c3fae8", "#96f2d7", "#63e6be", "#38d9a9", "#20c997", "#12b886", "#0ca678", "#099268", "#087f5b"],
            green:  ["#ebfbee", "#d3f9d8", "#b2f2bb", "#8ce99a", "#69db7c", "#51cf66", "#40c057", "#37b24d", "#2f9e44", "#2b8a3e"],
            lime:   ["#f4fce3", "#e9fac8", "#d8f5a2", "#c0eb75", "#a9e34b", "#94d82d", "#82c91e", "#74b816", "#66a80f", "#5c940d"],
            yellow: ["#fff9db", "#fff3bf", "#ffec99", "#ffe066", "#ffd43b", "#fcc419", "#fab005", "#f59f00", "#f08c00", "#e67700"],
            orange: ["#fff4e6", "#ffe8cc", "#ffd8a8", "#ffc078", "#ffa94d", "#ff922b", "#fd7e14", "#f76707", "#e8590c", "#d9480f"]
        };

        // create
        this._create();
        this._createValue();
        this._createInput();
        this._createEvents();

        return this.$tool;
    },

    // private
    _create: function()
    {
        this.$tool = $RE.dom('<div>');
        this.$tool.addClass('re-tool');
        this.$tool.attr('data-type', this.name);
    },
    _createValue: function()
    {
        this.value = this.instance[this.props.getter]();
    },
    _createInput: function()
    {
        this.$inputContainer = $RE.dom('<div>');
        this.$inputContainer.addClass('is-re-prepend');

        // prepend
        this.$inputPrepend = $RE.dom('<span>');

        // color select
        this.$colorSelect = $RE.dom('<b>');
        this.$colorSelect.addClass('re-color-select');


        // input
        this.$input = $RE.dom('<input>');
        this.$input.attr('type', 'text');

        // set value
        if (this.value === 'none')
        {
            this.$colorSelect.css('background-color', '#ffffff');
            this.$input.val('');
        }
        else
        {
            this.$colorSelect.css('background-color', this.value);
            this.$input.val(this.value);
        }


        // append
        this.$inputPrepend.append(this.$colorSelect);
        this.$inputContainer.append(this.$inputPrepend);
        this.$inputContainer.append(this.$input);

        this.$tool.append(this.$inputContainer);
    },
    _createEvents: function()
    {
        this.$input.on('keydown blur', this._setValue.bind(this));
        this.$colorSelect.on('click', this._openColor.bind(this));
    },
    _setValue: function(e)
    {
        if (e.type === 'keydown' && e.which !== 13) return;
        if (e.type === 'keydown') e.preventDefault();

        var value = '#' + this.$input.val().replace('#', '');

        this._setColor(value);
    },
    _setColor: function(value, reset)
    {
        // color select
        if (value === 'none')
        {
            this.$colorSelect.css('background-color', '#ffffff');
            this.$input.val('');
        }
        else this.$colorSelect.css('background-color', value);

        // setter
        this.instance[this.props.setter](value, reset);

        // adjust
        this.app.broadcast('adjustHeight');
        this.app.broadcast('changed', this, this.instance);
    },
    _openColor: function(e)
    {
        var $body = this.frame.getBody();
        var $target = $RE.dom(e.target).closest('.re-color-select');
        var $colorPicker = $body.find('#re-color-picker');

        if ($colorPicker.length !== 0)
        {
            $colorPicker.remove();
            $body.off('.revolvapp.color-picker');

            return;
        }

        this.$colorPicker = $RE.dom('<div>');
        this.$colorPicker.attr('id', 're-color-picker');
        this.$colorPicker.addClass('re-color-picker');

        // base colors
        var baseColors = this.opts.baseColors;
        if (this.props.prop === 'background-color')
        {
            if (baseColors.indexOf('none') === -1)
            {
                baseColors.unshift('none');
            }
        }
        else
        {
            var index = baseColors.indexOf('none');
            if (index !== -1) baseColors.splice(index, 1);
        }

        var $div = $RE.dom('<div class="re-base-colors">');
        for (var i = 0; i < baseColors.length; i++)
        {
            var color = baseColors[i];
            var $span = this._createColor(color, 'base', i);

            $div.append($span);
        }

        this.$colorPicker.append($div);

        // colors
        for (var key in this.colors)
        {
            var $div = $RE.dom('<div class="re-colors">');

            for (var i = 0; i < this.colors[key].length; i++)
            {
                var color = this.colors[key][i];
                var $span = this._createColor(color, key, i);

                $div.append($span);
            }

            this.$colorPicker.append($div);
        }

        // position
        var offset = this.$inputContainer.offset();
        var inputHeight = this.$inputContainer.height();

        this.$colorPicker.css({
            top: (offset.top + inputHeight) + 'px',
            left: offset.left + 'px'
        });

        $body.append(this.$colorPicker);
        $body.on('click.revolvapp.color-picker', this._closeColorPicker.bind(this));
        $body.on('keydown.revolvapp.color-picker', this._closeColorPicker.bind(this));

        // adjust
        this.app.broadcast('adjustHeight', this, this.$colorPicker.height());
    },
    _closeColorPicker: function(e)
    {
        var $target = $RE.dom(e.target).closest('.re-color-select');
        var $span = $RE.dom(e.target).closest('.re-color');
        if ($target.length !== 0)
        {
            return;
        }
        else
        {
            if ($span.length === 0) this._setColor(this.value, true);
            this.$colorPicker.remove();
        }

        this.frame.getBody().off('.revolvapp.color-picker');
        this.app.broadcast('adjustHeight');
    },
    _createColor: function(color, key, i)
    {
        var $span = $RE.dom('<span>');
        $span.attr('title', key + '-' + i);
        $span.attr('data-value', color);
        $span.addClass('re-color');

        if (color === 'none')
        {
            $span.addClass('re-color-none');
        }
        else
        {
            $span.css('background-color', color);
        }

        $span.on('click', this._selectColor.bind(this));
        $span.on('mouseover', this._selectColor.bind(this));

        return $span;
    },
    _selectColor: function(e)
    {
        var $select = $RE.dom(e.target);
        var value = $select.attr('data-value');

        this._setColor(value);

        value = (value === 'none') ? '' : value;
        this.$input.val(value);
    }
});
$RE.add('class', 'tool.url', {
    init: function(app, props, instance)
    {
        this.app = app;
        this.opts = app.opts;
        this.utils = app.utils;
        this.template = app.template;

        // local
        this.instance = instance;
        this.props = props;

        // create
        this._create();
        this._createValue();
        this._createInput();
        this._createEvents();

        return this.$tool;
    },

    // private
    _create: function()
    {
        this.$tool = $RE.dom('<div>');
        this.$tool.addClass('re-tool');
        this.$tool.attr('data-type', this.name);
    },
    _createInput: function()
    {
        // input
        this.$input = $RE.dom('<input>');
        this.$input.attr('type', 'url');

        if (this.value) this.$input.val(this.value);

        this.$tool.append(this.$input);
    },
    _createValue: function()
    {
        this.value = this.instance[this.props.getter]();
    },
    _createEvents: function()
    {
        this.$input.on('keydown blur', this._setUrl.bind(this));
    },
    _setUrl: function(e)
    {
        if (e.type === 'keydown' && e.which !== 13) return;
        if (e.type === 'keydown') e.preventDefault();

        var value = this.$input.val();

        this.instance[this.props.setter](value);

        // adjust
        this.app.broadcast('adjustHeight');
        this.app.broadcast('observeImages');
        this.app.broadcast('changed', this, this.instance);
    }

});
$RE.add('class', 'tool.size', {
    init: function(app, props, instance)
    {
        this.app = app;
        this.opts = app.opts;

        // local
        this.instance = instance;
        this.props = props;

        // create
        this._create();
        this._createValue();
        this._createInput();
        this._createEvents();

        return this.$tool;
    },

    // private
    _create: function()
    {
        this.$tool = $RE.dom('<div>');
        this.$tool.addClass('re-tool');
        this.$tool.attr('data-type', this.name);
    },
    _createValue: function()
    {
        this.value = this.instance[this.props.getter]();


/*


        else if (this.props.target === 'image')
        {
            this.value = 0; //this.$target.attr('width');
            //this.value = (this.value.search('%') === -1) ? this.value + 'px' : this.value;
        }
        else
        {
            this.value = this.$target.css(this.props.prop);
        }
*/

    },
    _createInput: function()
    {
        this.$input = $RE.dom('<input>');
        this.$input.attr('type', 'text');
        this.$input.val(this.value);

        this.$tool.append(this.$input);
    },
    _createEvents: function()
    {
        this.$input.on('keydown blur', this._setSize.bind(this));
    },
    _setSize: function(e)
    {
        if (e.type === 'keydown' && e.which !== 13) return;
        if (e.type === 'keydown') e.preventDefault();

        var value = this.$input.val();

        // setter
        this.instance[this.props.setter](value);

/*
        if (this.props.target === 'image')
        {
            this.$target.attr('width', value.replace('px', ''));
            this.$source.attr('width', value);
        }

*/


        // adjust
        this.app.broadcast('adjustHeight');
        this.app.broadcast('changed', this, this.instance);
    }

});
$RE.add('class', 'tool.upload-back', {
    init: function(app, props, instance)
    {
        this.app = app;
        this.opts = app.opts;
        this.card = app.card;
        this.utils = app.utils;
        this.upload = app.upload;
        this.template = app.template;

        // local
        this.instance = instance;
        this.props = props;
        this.defaultPosition = 'cover';

        // create
        this._create();
        this._createInput();
        this._createUpload();
        this._createValue();
        this._createEvents();

        return this.$tool;
    },

    // private
    _create: function()
    {
        this.$tool = $RE.dom('<div>');
        this.$tool.addClass('re-tool');
        this.$tool.attr('data-type', this.name);
    },
    _createValue: function()
    {
        this.value = this.instance[this.props.getter]();

        if (!this.value.image) return;

        if (this.opts.images)
        {
            var arr = this.value.image.split('/');
            var last = arr[arr.length-1];
            this.value.image = this.opts.images + last;
        }

        // img
        var $img = $RE.dom('<img>');
        $img.attr('src', this.value.image);

        this.upload.set($img);

        var patternChecked = (this.value.size === 'cover') ? false : true;
        this.$patternInput.attr('checked', patternChecked);
    },
    _createInput: function()
    {
        this.$uploadItem = $RE.dom('<input>');
        this.$uploadItem.attr('type', 'file');
        this.$uploadItem.attr('name', 'file');

        this.$patternLabel = $RE.dom('<label>');
        this.$patternLabel.html('Pattern');

        this.$patternInput = $RE.dom('<input>');
        this.$patternInput.attr('type', 'checkbox');

        this.$patternLabel.prepend(this.$patternInput);

        this.$tool.append(this.$uploadItem);
        this.$tool.append(this.$patternLabel);
    },
    _createUpload: function()
    {
        this.upload.build(this.$uploadItem, this._uploadComplete.bind(this), this._uploadRemove.bind(this));
    },
    _createEvents: function()
    {
        this.$patternInput.on('change', this._setBackgroundSize.bind(this));
    },
    _setBackgroundSize: function()
    {
        var size = (this.$patternInput.attr('checked')) ? 'auto' : 'cover';

        // setter
        this.instance[this.props.setter](false, size);
        this.app.broadcast('changed', this, this.instance);
    },
    _uploadRemove: function()
    {
        this.$patternInput.attr('checked', false);

        // setter
        this.instance[this.props.setter](false, false);
        this.app.broadcast('changed', this, this.instance);
    },
    _uploadComplete: function(response)
    {
        var size = (this.$patternInput.attr('checked')) ? 'auto' : 'cover';

        // setter
        this.instance[this.props.setter](response.url, size);
        this.app.broadcast('changed', this, this.instance);
    }
});
$RE.add('class', 'tool.upload', {
    init: function(app, props, instance)
    {
        this.app = app;
        this.opts = app.opts;
        this.card = app.card;
        this.utils = app.utils;
        this.upload = app.upload;
        this.template = app.template;

        // local
        this.instance = instance;
        this.props = props;

        // create
        this._create();
        this._createInput();
        this._createUpload();
        this._createValue();

        return this.$tool;
    },

    // private
    _create: function()
    {
        this.$tool = $RE.dom('<div>');
        this.$tool.addClass('re-tool');
        this.$tool.attr('data-type', this.name);
    },
    _createValue: function()
    {
        this.value = this.instance[this.props.getter]();

        // img
        var $img = $RE.dom('<img>');

        if (this.opts.images)
        {
            var arr = this.value.split('/');
            var last = arr[arr.length-1];
            this.value = this.opts.images + last;
        }

        $img.attr('src', this.value);

        this.upload.set($img, false);
    },
    _createInput: function()
    {
        this.$uploadItem = $RE.dom('<input>');
        this.$uploadItem.attr('type', 'file');
        this.$uploadItem.attr('name', 'file');

        this.$tool.append(this.$uploadItem);
    },
    _createUpload: function()
    {
        this.$upload = this.upload.build(this.$uploadItem, this._uploadComplete.bind(this), false, 120);
    },
    _uploadComplete: function(response)
    {
        this.instance[this.props.setter](response.url);
        this.app.broadcast('changed', this, this.instance);
    }
});
$RE.add('module', 'builder', {
    init: function(app)
    {
        this.app = app;
        this.opts = app.opts;
        this.frame = app.frame;
        this.panel = app.panel;
        this.template = app.template;
    },

    // messages
    onmessage: {
        loaded: function()
        {
            this._build();
        }
    },

    // private
    _build: function()
    {
        this.frame.setCode(this.template.getInitialCode());
        this.frame.load(this._load.bind(this));

    },
    _load: function()
    {
        this.panel.build();
        this.template.build(this.frame.getSource());

        // built
        setTimeout(function()
        {
            this.frame.buildCss();
            this.frame.buildUndo();
            this.frame.buildClick();

            setTimeout(function()
            {
                this.app.broadcast('built', this);
                this.app.broadcast('composed', this);
                this.app.broadcast('images.observe', this);
                this.app.broadcast('adjustHeight', this);

                this.frame.getElement().css('visibility', 'visible');

            }.bind(this), 50);

        }.bind(this), 50);
    }
});
$RE.add('module', 'main', {
    init: function(app)
    {
        this.app = app;
        this.opts = app.opts;
        this.frame = app.frame;
        this.slide = app.slide;
        this.upload = app.upload;
        this.control = app.control;
        this.element = app.element;
        this.dropdown = app.dropdown;
        this.template = app.template;

        // starter
        this.app.starter(this, 10);
    },
    start: function()
    {
        this._build();
    },

    // messages
    onmessage: {
        changed: function(sender)
        {
            this._autosave();
        },
        adjustHeight: function(sender, value)
        {
            this.frame.adjustHeight(value);
        },
        images: {
            observe: function(sender)
            {
                this._observeImages();
            }
        },
        readonly: {
            enable: function()
            {
                var $body = this.frame.getBody();

                $body.find('[contenteditable]').attr('contenteditable', false);
                $body.find('.re-block').removeClass('is-re-active').addClass('is-re-readonly');
                $body.find('.re-card').removeClass('is-re-active');

                this.control.hide('card');
                this.control.hide('block');

                this.slide.close();
                this.dropdown.close();
            },
            disable: function()
            {
                var $body = this.frame.getBody();

                $body.find('[contenteditable]').attr('contenteditable', true);
                $body.find('.re-block').removeClass('is-re-readonly');

                this.control.show('card');
            }
        }
    },

    // private
    _build: function()
    {
        this._buildFrame();
        this._buildTemplate();
    },
    _buildFrame: function()
    {
        this.$frame = this.frame.getElement();
        this.$frame.attr('scrolling', 'no');
        this.$frame.css(this.opts.frame);
        this.$frame.css({
            'display': 'block',
            'visibility': 'hidden'
        });
    },
    _buildTemplate: function()
    {
        $RE.ajax.get({
            url: this.opts.template,
            success: this._buildCode.bind(this)
        });
    },
    _buildCode: function(source)
    {
        this.frame.setSource(source);

        // loaded
        this.app.broadcast('loaded', this);
    },
    _observeImages: function()
    {
        var self = this;
        var $images = this.frame.getCards().find('[data-element-type="image"]');

        $images.each(function(node)
        {
            var $img = $RE.dom(node);
            var complete = function(response)
            {
                var instance = $img.dataget('instance');
                instance.setImage(response.url);
            };

            if (self.opts.upload !== false) {
                self.upload.push($img, complete);
            }
        });
    },
    _autosave: function()
    {
        if (this.opts.autosave === false) return;

        var html = this.frame.getHtml();
        var template =  this.frame.getTemplate();

        $RE.ajax.post({
            url: this.opts.autosave,
            data: { html: html, template: template }
        });
    }
});
$RE.add('module', 'preview', {
    init: function(app)
    {
        this.app = app;
        this.opts = app.opts;
        this.frame = app.frame;
        this.panel = app.panel;
        this.template = app.template;

        // local
        this.previewButtons = {
            desktop: {
                name: 'desktop',
                icon: this.opts.icons['desktop'],
                callback: this._toggle.bind(this)
            },
            mobile: {
                name: 'mobile',
                icon: this.opts.icons['mobile'],
                callback: this._toggle.bind(this)
            }
        };
    },

    // messages
    onmessage: {
        built: function()
        {
            this._build();
        }
    },

    // private
    _build: function()
    {
        for (var key in this.previewButtons)
        {
            this.panel.add(this.previewButtons[key], 'left');
        }

        // active
        this.panel.addActive('desktop');
    },
    _toggle: function(name)
    {
        var $frame = this.frame.getElement();
        var message = (name === 'desktop') ? 'readonly.disable' : 'readonly.enable';
        var width = (name === 'desktop') ? this.opts.frame.width : this.opts.widthPreview.mobile + 'px';

        // set width
        $frame.css('width', width);

        if (name === 'mobile')
        {
            var $sourceBody = this.template.getSourceBody();

            this.panel.addActive('mobile');
            this.panel.disableButtons(['desktop', 'mobile']);
        }
        else
        {
            this.panel.addActive('desktop');
            this.panel.enableButtons();
        }

        // broadcast
        this.app.broadcast('adjustHeight', this);
        this.app.broadcast('setControlPosition');
        this.app.broadcast(message, this);
    }
});
$RE.add('module', 'code', {
    init: function(app)
    {
        this.app = app;
        this.opts = app.opts;
        this.tidy = app.tidy;
        this.utils = app.utils;
        this.panel = app.panel;
        this.frame = app.frame;
        this.element = app.element;
        this.template = app.template;

        // local
        this.$textarea = false;
        this.codeButton = {
            name: 'code',
            icon: this.opts.icons['code'],
            callback: this._toggle.bind(this)
        };
    },

    // messages
    onmessage: {
        built: function()
        {
            if (this.opts.code)
            {
                this._build();
            }
        }
    },

    // private
    _build: function()
    {
        this.panel.add(this.codeButton, 'right');
    },
    _buildTextarea: function(code, height)
    {
        this.$textarea = $RE.dom('<textarea>');
        this.$textarea.addClass('re-editor-textarea');
        this.$textarea.attr('data-gramm_editor', false);
        this.$textarea.height(height);
        this.$textarea.val(code);

        this.$textarea.on('keydown', this._handleKeydown.bind(this));

        var $element = this.element.getElement();
        $element.append(this.$textarea);
    },
    _handleKeydown: function(e)
    {
        var keyCode = e.keyCode || e.which;
        var textarea = this.$textarea.get();

        if (keyCode === 9)
        {
            e.preventDefault();
            var s = textarea.selectionStart;
            textarea.value = textarea.value.substring(0, textarea.selectionStart) + "    " + textarea.value.substring(textarea.selectionEnd);
            textarea.selectionEnd = s + 4;
        }
    },
    _toggle: function()
    {
        var code;
        var $frame = this.frame.getElement();

        if (this.$textarea === false)
        {
            code = this.template.getSource();
            code = this.tidy.get(code);
            code = this.utils.replaceRgbToHex(code);

            $frame.hide();

            this._buildTextarea(code, $frame.height());

            this.panel.addActive('code');
            this.panel.disableButtons(['code']);
        }
        else
        {
            code = this.$textarea.val();
            this.html = code;

            this.$textarea.remove();
            this.$textarea = false;

            this.frame.setCode(this.template.getInitialCode());
            this.frame.load(this._load.bind(this));

            $frame.show();

            this.panel.addActive('desktop');
            this.panel.enableButtons();
        }
    },
    _load: function()
    {
        this.template.build(this.html);

        // built
        setTimeout(function()
        {
            this.frame.buildCss();
            this.frame.buildUndo();
            this.frame.buildClick();

            this.app.broadcast('rebuilt', this);
            this.app.broadcast('images.observe', this);
            this.app.broadcast('adjustHeight', this);

        }.bind(this), 100);
    }
});
$RE.add('module', 'card', {
    init: function(app)
    {
        this.app = app;
        this.$win = app.$win;
        this.opts = app.opts;
        this.tool = app.tool;
        this.utils = app.utils;
        this.slide = app.slide;
        this.frame = app.frame;
        this.control = app.control;
        this.animate = app.animate;
        this.editable = app.editable;
        this.dropdown = app.dropdown;
        this.template = app.template;
    },

    // messages
    onmessage: {
        built: function()
        {
            this._build();
        },
        rebuilt: function()
        {
            this._build();
        },
        changed: function()
        {
            this._setControlPosition();
        },
        setControlPosition: function()
        {
            this._setControlPosition();
        },
        card: {
            settings: function(sender, e, $card)
            {
                this._toggleSettings($card);
            },
            duplicate: function(sender, e, $card)
            {
                this._duplicate($card);
            },
            trash: function(sender, e, $card)
            {
                this._trash($card);
            }
        },
        block: {
            added: function(sender, $block)
            {
                this._rebuildBlock($block);
            },
            duplicated: function(sender, $block)
            {
                this._rebuildBlock($block);
            },
            trashed: function(sender, $card)
            {
                this._checkBlocks($card);
            }
        }
    },

    // private
    _checkBlocks: function($card)
    {
        var $blocks = $card.find('.re-block');

        if ($blocks.length === 0)
        {
            var $add = $RE.dom('<div>');
            $add.attr('id', 're-add-block-icon');
            $add.html(this.opts.icons['add-circle']);
            $add.on('click', function()
            {
                this.app.broadcast('block.addToCard', this, $card);

            }.bind(this));

            $card.find('td').append($add);

            // hide control
            var id = $card.attr('data-element-id');
            this.control.hide('card', id);
        }
    },
    _toggleSettings: function($card)
    {
        $card.removeClass('is-re-hover');
        $card.addClass('is-re-active');

        this.dropdown.build($card, this.tool.build($card));
    },
    _duplicate: function($card)
    {
        this.template.createSnapshot();
        this.slide.close();
        this.dropdown.close();

        // remove hover
        $card.removeClass('is-re-hover');

        // get card data
        var instance = $card.dataget('instance');
        var $source = instance.getSource();
        var $clone = $source.clone();

        // check spacer
        var target = this._addSpacer($card, $source);

        // add to source template
        target.$source.after($clone);

        // parse
        var $newcard = this.template.parse($clone);

        // add to source template
        target.$card.after($newcard);

        // rebuild
        this._buildCard($newcard);

        // scroll & shake
        setTimeout(function()
        {
            var tolerance = 20;
            var offset = $newcard.offset();
            var frameOffset = this.frame.getElement().offset();
            var callback = function()
            {
                this.animate.start($newcard, 'shake', this._duplicated.bind(this));

            }.bind(this);

            // broadcast
            this.app.broadcast('adjustHeight', this);

            // scroll to
            this.utils.scrollTo(document, frameOffset.top + offset.top - tolerance, 500, callback);

        }.bind(this), 10);

    },
    _duplicated: function($card)
    {
        this.app.broadcast('card.duplicated', this, $card);
        this.app.broadcast('changed', this, $card);
    },
    _trash: function($card)
    {
        this.template.createSnapshot();
        this.slide.close();
        this.dropdown.close();
        this.animate.start($card, 'fadeOut', this._trashed.bind(this));
    },
    _trashed: function($card)
    {
        var id = $card.attr('data-element-id');
        var instance = $card.dataget('instance');
        var $source = instance.getSource();

        // remove control
        this.control.remove('card', id);

        // remove spacer
        this._removeSpacer($card);

        // remove card
        $source.remove();
        $card.remove();

        // broadcast
        this.app.broadcast('card.trashed', this);
        this.app.broadcast('adjustHeight', this);
        this.app.broadcast('changed', this);
    },
    _addSpacer: function($card, $source)
    {
        var instance, $sourceSpacer;
        var target = {};
        var $spacer = $card.nextElement();

        if ($spacer.attr('data-element-type') === 'spacer')
        {
            // spacer exists
            instance = $spacer.dataget('instance');
            $sourceSpacer = instance.getSource();

            target = { $card: $spacer, $source: $sourceSpacer };
        }
        else if ($spacer.attr('data-element-type') !== 'spacer' && this.opts.card.spacer !== false)
        {
            // spacer not exist
            $sourceSpacer = $RE.dom('<re-spacer height="' + this.opts.card.spacer + '">');
            $spacer = this.template.create('spacer', { $source: $sourceSpacer });

            $card.after($spacer);
            $source.after($sourceSpacer);

            target = { $card: $spacer, $source: $sourceSpacer };
        }
        else
        {
            target = { $card: $card, $source: $source };
        }

        return target;
    },
    _removeSpacer: function($card)
    {
        var $spacer = $card.prevElement();
        if ($spacer.attr('data-element-type') === 'spacer')
        {
            var instance = $spacer.dataget('instance');
            var $sourceSpacer = instance.getSource();

            $spacer.remove();
            $sourceSpacer.remove();
        }
    },
    _build: function()
    {
        if (this.opts.edit === false)
        {
            this.frame.getCards().removeClass('re-card');
            return;
        }

        this.frame.getCards().each(this._buildCard.bind(this));
    },
    _buildCard: function(card, index)
    {
        var $card = $RE.dom(card);

        // control
        this.control.build('card', $card, (index === 0));

        // build blocks
        var $blocks = $card.find('.re-block');
        $blocks.each(function(block, i)
        {
            this._buildBlock(block, index, i);

        }.bind(this));

        this._repositionControls();
        this._checkBlocks($card);
    },
    _rebuildBlock: function($block)
    {
        var $card = $block.closest('.re-card');
        var len = $card.find('.re-block').length;

        $card.find('#re-add-block-icon').remove();

        this._buildBlock($block);
        this.app.broadcast('images.observe', this);
    },
    _repositionControls: function()
    {
        var win = this.frame.getWin();
        var $win = $RE.dom(win);

        this.$win.off('resize.revolvapp-controls');
        $win.off('resize.revolvapp-controls');

        this.$win.on('resize.revolvapp-controls', this._setControlPosition.bind(this));
        $win.on('resize.revolvapp-controls', this._setControlPosition.bind(this));
    },
    _setControlPosition: function()
    {
        var $body = this.frame.getBody();
        var $controls = $body.find('.re-controls');

        $controls.each(function(node)
        {
            var $control = $RE.dom(node);
            var id = $control.attr('data-control-id');
            var $el = $body.find('[data-element-id="' + id + '"]');
            var $target = ($el.hasClass('re-card')) ? $el : $el.closest('.re-card');
            var bodyWidth = $body.width();
            var minWidth = 680;
            var width = $target.innerWidth();

            var height = $el.innerHeight();
            var elOffset = $el.offset();
            var offset = $target.offset();
            var shift = (bodyWidth < minWidth) ? -40 : 4;
            var top = elOffset.top + 'px';
            var left = (offset.left + width + shift) + 'px';

            $control.css({
                'top': top,
                'left': left
            });

        });

        this.app.broadcast('adjustHeight');

    },
    _buildBlock: function(block)
    {
        var $block = $RE.dom(block);

        // editable
        $block.find('.re-editable').each(this._buildEditable.bind(this));

        // prevent link click
        $block.find('a').on('click', function(e) { e.preventDefault(); });

        // click
        $block.on('click.revolvapp', this._handleBlockClick.bind(this))
    },
    _buildEditable: function(el)
    {
        this.editable.build(el);
    },
    _handleBlockClick: function(e)
    {
        var $el = $RE.dom(e.target).closest('.re-block');

        this.app.broadcast('block.click', this, e, $el);
    }
});
$RE.add('module', 'block', {
    init: function(app)
    {
        this.app = app;
        this.opts = app.opts;
        this.tool = app.tool;
        this.frame = app.frame;
        this.utils = app.utils;
        this.slide = app.slide;
        this.control = app.control;
        this.animate = app.animate;
        this.template = app.template;
    },

    // messages
    onmessage: {
        block: {
            addToCard: function(sender, $card)
            {
                this._toggleAdd($card, 'card');
            },
            add: function(sender, e, $block)
            {
                this._toggleAdd($block);
            },
            settings: function(sender, e, $block)
            {
                this._toggleSettings($block);
            },
            duplicate: function(sender, e, $block)
            {
                this._duplicate($block);
            },
            trash: function(sender, e, $block)
            {
                this._trash($block);
            },
            click: function(sender, e, $block)
            {
                if (!$block.hasClass('is-re-readonly'))
                {
                    this._active($block);
                }
            }
        }
    },

    // private
    _active: function($block)
    {
        var $body = this.frame.getBody();
        $body.find('.re-block').removeClass('is-re-active');
        $body.find('.re-card').removeClass('is-re-active');
        $body.off('keydown.revolvapp-shortcuts');

        this.control.hide('card');
        this.control.hide('block');

        $block.addClass('is-re-active');
        $body.on('keydown.revolvapp-shortcuts', this._handleShortcuts.bind(this));

        if ($body.find('#re-slide').length === 0)
        {
            this.control.build('block', $block)
        }
        else
        {
            setTimeout(function()
            {
                this.control.build('block', $block)

            }.bind(this), 400);
        }
    },
    _handleShortcuts: function(e)
    {
        var key = e.which;
        var ctrlKey = ((e.ctrlKey || e.metaKey) && !e.altKey);
        var $body = this.frame.getBody();
        var $block = $body.find('.re-block.is-re-active');

        if ($block.length !== 0)
        {
            // Ctrl + Shift + O
            if (ctrlKey && e.shiftKey && key === 79)
            {
                e.preventDefault();
                this._toggleSettings($block);
            }
            // Ctrl + Shift + A
            else if (ctrlKey && e.shiftKey && key === 65)
            {
                e.preventDefault();
                this._toggleAdd($block);
            }
            // Ctrl + Shift + Backspace
            else if (ctrlKey && e.shiftKey && key === 8)
            {
                e.preventDefault();
                this._trash($block);
            }
            // tab
            else if (!ctrlKey && !e.shiftKey && key === 9)
            {
                e.preventDefault();

                var $nextBlock = $block.next();
                if ($nextBlock.hasClass('re-block'))
                {
                    this._active($nextBlock);
                }
                else
                {
                    var $card = $block.closest('.re-card');
                    $nextBlock = $card.next().find('.re-block').first();
                    if ($nextBlock.hasClass('re-block'))
                    {
                        this._active($nextBlock);
                    }
                }
            }
        }
    },
    _toggleAdd: function($el, type)
    {
        var id = $el.attr('data-element-id');
        var $html = $RE.dom('<div>');
        $html.addClass('re-add-blocks');

        for (var name in this.opts.blocks)
        {
            var blocks = this.opts.blocks[name];
            var $head = $RE.dom('<h5>');
            $head.html(name);
            $html.append($head);

            var $blocksContainer = $RE.dom('<div>');

            var z = 0;
            for (var key in blocks)
            {
                z++;

                var $span = $RE.dom('<span>');
                $span.attr('data-type', key);
                $span.attr('data-name', name);
                $span.attr('data-block', id);
                $span.attr('data-card', (type === 'card'));

                $span.on('click', this._addBlock.bind(this));

                var $icon = $RE.dom('<img>');
                $icon.attr('src', this.opts.path + 'img/svg/' + key +'.svg?v');

                $span.append($icon);
                $blocksContainer.append($span);

                if (z === 3)
                {
                    $blocksContainer.append('<br>');
                    z = 0;
                }
            }

            $html.append($blocksContainer);

        }

        this.slide.build($el, $html);
    },
    _addBlock: function(e)
    {
        this.template.createSnapshot();
        this.slide.close();

        var $span = $RE.dom(e.target).closest('span');
        var type = $span.attr('data-type');
        var name = $span.attr('data-name');
        var card = $span.attr('data-card');
        var blockId = $span.attr('data-block');
        var $el = this.frame.getBody().find('[data-element-id="' + blockId +'"]');
        var instance = $el.dataget('instance');
        var $source = instance.getSource();

        var code = this.opts.blocks[name][type];

        code = code.replace(/{{\-\-image\-placeholder\-\-}}/gi, this.opts.path + 'img/image-placeholder.png');
        code = code.replace(/{{\-\-app\-store\-placeholder\-\-}}/gi, this.opts.path + 'img/badge-app-store.png');
        code = code.replace(/{{\-\-google\-play\-placeholder\-\-}}/gi, this.opts.path + 'img/badge-google-play.png');

        var $code = $RE.dom('<div>').html(code).children().first();
        var $newblock = this.template.parse($code);

        if (card)
        {
            $el.find('td').html($newblock);
            $source.html($code);
        }
        else
        {
            $el.after($newblock);
            $source.after($code);
        }

        this._active($newblock);
        this.app.broadcast('adjustHeight', this);
        this.app.broadcast('block.added', this, $newblock);
        this.app.broadcast('changed', this, $newblock);

        var tolerance = 20;
        var offset = $newblock.offset();
        var frameOffset = this.frame.getElement().offset();

        // scroll to
        this.utils.scrollTo(document, frameOffset.top + offset.top - tolerance, 500);
    },
    _toggleSettings: function($block)
    {
        this.slide.build($block, this.tool.build($block));
    },
    _duplicate: function($block)
    {
        this.template.createSnapshot();
        this.slide.close();

        // get data
        var instance = $block.dataget('instance');
        var $source = instance.getSource();
        var $clone = $source.clone();

        // add to source template
        $source.after($clone);

        // parse
        var $newblock = this.template.parse($clone);

        // add to source template
        $block.after($newblock);

        // broadcast
        this.app.broadcast('adjustHeight', this);

        // scroll
        setTimeout(function()
        {
            var tolerance = 10;
            var offset = $newblock.offset();
            var frameOffset = this.frame.getElement().offset();

            this.utils.scrollTo(document.body, frameOffset.top + offset.top - tolerance, 500);
            this.app.broadcast('block.duplicated', this, $newblock);
            this.app.broadcast('changed', this, $newblock);
            this._active($newblock);

        }.bind(this), 10);

    },
    _trash: function($block)
    {
        this.template.createSnapshot();
        this.slide.close();
        this.animate.start($block, 'fadeOut', this._trashed.bind(this));
    },
    _trashed: function($block)
    {
        var id = $block.attr('data-element-id');
        var instance = $block.dataget('instance');
        var $source = instance.getSource();
        var $card = $block.closest('.re-card');

        // remove control
        this.control.remove('block', id);
        this.control.show('card');

        // remove block
        $source.remove();
        $block.remove();

        // broadcast
        this.app.broadcast('block.trashed', this, $card);
        this.app.broadcast('adjustHeight', this);
        this.app.broadcast('changed', this);
    }
});

    window.Revolvapp = window.$RE = $RE;
}());