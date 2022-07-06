const observer = new MutationObserver((mutationsList) => {
	mutationsList.forEach((mutation) => {
		if (mutation.addedNodes.length) {
			console.log('Agregado', mutation.addedNodes[0]);
		} else {
			console.log('no se encontro elemento');
		}
	});
});

setTimeout(() => {
	const elementos = document.querySelector('#option_select');
	console.log(elementos);

	const observerOptions = {
		childList: true,
		subtree: true,
	};

	observer.observe(elementos, observerOptions);
}, 8000);
