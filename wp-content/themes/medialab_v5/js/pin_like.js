'strict'

// Production steps of ECMA-262, Edition 5, 15.4.4.19
// Reference: http://es5.github.com/#x15.4.4.19
if (!Array.prototype.map) {

  Array.prototype.map = function (callback, thisArg) {

    var T, A, k;

    if (this == null) {
      throw new TypeError(" this is null or not defined");
    }

    // 1. Let O be the result of calling ToObject passing the |this| value as the argument.
    var O = Object(this);

    // 2. Let lenValue be the result of calling the Get internal method of O with the argument "length".
    // 3. Let len be ToUint32(lenValue).
    var len = O.length >>> 0;

    // 4. If IsCallable(callback) is false, throw a TypeError exception.
    // See: http://es5.github.com/#x9.11
    if (typeof callback !== "function") {
      throw new TypeError(callback + " is not a function");
    }

    // 5. If thisArg was supplied, let T be thisArg; else let T be undefined.
    if (thisArg) {
      T = thisArg;
    }

    // 6. Let A be a new array created as if by the expression new Array( len) where Array is
    // the standard built-in constructor with that name and len is the value of len.
    A = new Array(len);

    // 7. Let k be 0
    k = 0;

    // 8. Repeat, while k < len
    while (k < len) {

      var kValue, mappedValue;

      // a. Let Pk be ToString(k).
      //   This is implicit for LHS operands of the in operator
      // b. Let kPresent be the result of calling the HasProperty internal method of O with argument Pk.
      //   This step can be combined with c
      // c. If kPresent is true, then
      if (k in O) {

        var Pk = k.toString(); // This was missing per item a. of the above comment block and was not working in IE8 as a result

        // i. Let kValue be the result of calling the Get internal method of O with argument Pk.
        kValue = O[Pk];

        // ii. Let mappedValue be the result of calling the Call internal method of callback
        // with T as the this value and argument list containing kValue, k, and O.
        mappedValue = callback.call(T, kValue, k, O);

        // iii. Call the DefineOwnProperty internal method of A with arguments
        // Pk, Property Descriptor {Value: mappedValue, Writable: true, Enumerable: true, Configurable: true},
        // and false.

        // In browsers that support Object.defineProperty, use the following:
        // Object.defineProperty( A, Pk, { value: mappedValue, writable: true, enumerable: true, configurable: true });

        // For best browser support, use the following:
        A[Pk] = mappedValue;
      }
      // d. Increase k by 1.
      k++;
    }

    // 9. return A
    return A;
  };
}

var rowheights = []; var nb_columns = 3;
if (document.URL.match('/?s=')) nb_columns = 4;
// FIX SIZE OF PIN_LIKE PIECES
function realign_columns() {
	// @todo 
	var pins = $(".column_display:visible"), // only visible ones
			nb_columns = +pins.first().parent().attr('data-columns') || 3,// data columns attribute OR default
			queue = []; // queue

	// console.log(pins.first().parent().attr('data-columns'));

	if(nb_columns < 2) {
		// console.log('%c realign_columns()', 'background-color:green; color: white', 'deactivated - cfr data-column html attribute' );
		return;
	};

	// console.log('%c realign_columns()', 'background-color:green; color: white');
	// console.log('', pins.length, '- n. columns', nb_columns);

	// calculate maximum height per row, easy enough
	pins.each(function(i, e) {
		var col = i % nb_columns,
				h = $(e).height(),
				title = $(e).find('h2').text(),
				maxh = 0; 

		if(col == 0 && i != 0){ // new row, cloture previous
			// console.log('  - clear elements', col, queue.length, queue);
			// calculate maximum and clear
			maxh = Math.max.apply(this, queue.map(function(d){return d.h}))
			
			for(var j in queue) {
				queue[j].el.height(maxh);
			}
			queue = [];
		}
		// console.log('  ', i, '- n. in col', col, '- height', h, '- title', title);
		
		queue.push({h:h, el:$(e)});

		
	});


	return;
		for (var i = 0; i < Math.floor($(".column_display:visible").length/nb_columns); i++) rowheights[i] = 0;
	$(".column_display:visible").each(function(idx) {
		var row = Math.floor(idx/nb_columns);
		rowheights[row] = Math.max(rowheights[row], $(this).height());
	});
	$(".column_display:visible").each(function(idx) {
		$(this).height(rowheights[Math.floor(idx/nb_columns)]);
		debugger;
	});
}

$(document).ready(function(){
	realign_columns();

	// CHANGE BACKGROUND PATTERN
	$(".change-bg-1").click(function() {
		$("body").css("background-image","url(http://www.medialab.sciences-po.fr/wp-content/themes/medialab_v4/img/bg.jpg)");
	});

	$(".change-bg-2").click(function() {
		$("body").css("background-image","url(http://www.medialab.sciences-po.fr/wp-content/themes/medialab_v4/img/bg-alt-3.jpg)");
	});

	// SORT OBJECTS BY PEOPLE/PROJECTS/TOOLS
	$(".facet").click(function() {
		var facet = $(this),
				target = facet.attr('data-target') || facet.attr('id');

		if(facet.hasClass("active_facet")) {
			$(".column_display").css("display","block");
			$(".active_facet").removeClass("active_facet");
			$(".active-p-enabled").removeClass("active-p-enabled");
		} else {
			$(".active-p-enabled").removeClass("active-p-enabled");
			$(".active_facet").removeClass("active_facet");
			facet.addClass("active_facet");
			// remove all elements
			$(".column_display").each(function(i, e){
				var el = $(this);
				if(!el.hasClass(target))
					el.hide();
				else
					el.show();
				
			})
			$("#" + target).addClass("active_facet").css("display","block");
		}
		realign_columns();
	});

	// SORT PEOPLE/PROJECTS BY ACTIVE/INACTIVE STATUS
	if($(".column_display").hasClass("0")){
		$(".0").css("display", "none");
	};
	$(".active-p").click(function active_p() {
		$(".active_facet").removeClass("active_facet");
		if ($(this).hasClass("active-p-enabled")){
			$(".column_display").css("display","block");
			$(".active-p-enabled").removeClass("active-p-enabled");
			$(".actif").css("display", "block");
			$(".inactif").css("display", "block");
		} else {
			$(".active-p-enabled").removeClass("active-p-enabled");
			$(this).addClass("active-p-enabled");
			$(".actif").css("display", "block");
			$(".inactif").css("display","none");
		}
		realign_columns();
	});
	$(".retired-p").click(function() {
		$(".active_facet").removeClass("active_facet");
		if ($(this).hasClass("active-p-enabled")){
			$(".column_display").css("display","block");
			$(".active-p-enabled").removeClass("active-p-enabled");
			$(".actif").css("display","block");
			$(".inactif").css("display", "block");
		} else {
			$(".active-p-enabled").removeClass("active-p-enabled");
			$(this).addClass("active-p-enabled");
			$(".inactif").css("display", "block");
			$(".actif").css("display", "none");
		}
		realign_columns();
	});

	// HIDE SORTER IF EMPTY 
	if($(".related-tools-content").html() == ''){
		$(".related-tools").css("display", "none");
	}
	if($(".related-people-content").html() == ''){
		$(".related-people").css("display", "none");
	}
	if($(".related-projects-content").html() == ''){
		$(".related-projects").css("display", "none");
	}
	if($(".related-publications-content").html() == ''){
		$(".related-publications").css("display", "none");
	}
});
