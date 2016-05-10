/* Initialize variables */
var randomID;
var randomInfo;

/* All of the magic happens on the button click */
var randomIDset = function() {

	/* Choose a random ID and set it to the randomID variable */
	$(document).ready(function(){
		$.ajax({
			type: "GET",
			dataType: "json",
			async: "false",
			url: "public/php/rand-id.php",
			success: function(data) {
				randomID = data;
				randomCaller();
			}
		});
	});

	/* Call the information about the ID w/ a PHP proxy */
	function randomCaller() {
		$(document).ready(function(){
			$.ajax({
				type: "GET",
				dataType: "html",
				async: "false",
				url: "public/php/proxy.php",
				headers: {
					"X-Proxy-Url": "http://fcaw.library.umass.edu/X?op=find-doc&doc_num=0" + randomID + "&base=FCL01",
				},
				success: randomWriter
			});
		});
	};

	/* The function that does the writing */
	function randomWriter(xml) {
		$(xml).find("oai_marc").each(function(){

				// This sort of gets the job done in limited cases, but I'm going to have
				// to rewrite this using if-then statements in order to capture the
				// idiosyncratic nature of the markup. 
				$("#title").html($(this).find('varfield[id="245"] subfield[label="a"]'));
				$("#subtitle").html($(this).find('varfield[id="245"] subfield[label="b"]'));
				// Subject is hard b/c there's weird multiples of things that smoosh up 
				// next to each other. 
				$("#subject").html($(this).find('varfield[id="650"] subfield[label="a"]'));

				// e.g. (Note that this involves more or less trial-and-error to see what
				// values are most common in the dataset for each field, and then going 
				// from there.)
				if ( ($(this).find('varfield[id="100"] subfield[label="a"]').text() ) == "" ) {
					$("#author").html($(this).find('varfield[id="245"] subfield[label="c"]'));
				} else {
					$("#author").html($(this).find('varfield[id="100"] subfield[label="a"]'));
				};
		});

		/* Set the hyperlink to the appropriate ID and write them out at the same time as metadata*/
		$(document).ready(function(){
			$("#link").html("<a href='https://fcaw.library.umass.edu/F/?func=direct&doc_number=0" + randomID + "&doc_library=FCL01' target='_blank'>Just go to my library page.</a>");
			$("#checkoutlink").html("<a href='https://fcaw.library.umass.edu/F/?func=title-request-form&bib_doc_number=0" + randomID + "target='_blank'>Check me out!</a>");
		});
	};
}