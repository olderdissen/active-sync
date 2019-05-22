function rte_counter()
	{
	////////////////////////////////////////////////////////////////////////////////
	// get access to editor
	////////////////////////////////////////////////////////////////////////////////

	var o = document.getElementById(rte_name);

	////////////////////////////////////////////////////////////////////////////////
	// get html and mail part
	////////////////////////////////////////////////////////////////////////////////

	var h = o.contentWindow.document.body.innerHTML;
	var p = h.replace(/<br>/g, "\n");

	////////////////////////////////////////////////////////////////////////////////
	// calculate number of remaining chars and number of needed messages
	////////////////////////////////////////////////////////////////////////////////

	var l = p.length;
	var m = 160; // a single message has maximum of 160 chars
	var c = 0x01; // 0x00 - 0xFF

	while(l > m)
		{
		l = l - 153;
		m = 153; // message is split into 153 char parts
		c = c + 0x01;
		}

	////////////////////////////////////////////////////////////////////////////////
	// create new html code
	////////////////////////////////////////////////////////////////////////////////

	var d = document.createElement('div');

	d.style.color = "#000000";
	d.style.color = (c > 0x05 ? "#C00000" : d.style.color); // android specific
	d.style.color = (c > 0xFF ? "#00C000" : d.style.color); // protocol specific

	d.innerHTML = "SMS: " + (m - l) + " / " + c;

	////////////////////////////////////////////////////////////////////////////////
	// insert html code
	////////////////////////////////////////////////////////////////////////////////

	var o = document.getElementById("charcounter");

	o.innerHTML = d.outerHTML;
	}

