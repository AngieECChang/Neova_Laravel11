jQuery.fn.appendDom = function(template) {
  return this.each(function() {
    for (element in template) {
      var el = (typeof(template[element].tagName) === 'string') ?
        document.createElement(template[element].tagName): document.createTextNode('');
      delete template[element].tagName;
      for (attrib in template[element]) {
        switch ( typeof(template[element][attrib]) ) {
          case 'string' :
            if ( typeof(el[attrib]) === 'string' ) {
             el[attrib] = template[element][attrib];
            } else {
              el.setAttribute(attrib, template[element][attrib]);
            }
            break;
          case 'function':
            el[attrib] = template[element][attrib];
            break;
          case 'object' :
            if (attrib === 'childNodes')  {$(el).appendDom(template[element][attrib]);}
            break;
        }
      }
      this.appendChild(el);
    }
  });
};