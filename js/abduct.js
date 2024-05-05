
/**
 * abduct (V2)
 */
async function abduct(element, implants) {

	async function spawn(implant, element, child) {
		if (!child) {
			child = document.createElement(implant.tag || "div");
			if (implant.class) {
				child.className = implant.class;
			}
			element.appendChild(child);
			if (implant.init) {
				await implant.init(child, implant, () => spawn(implant, element, child));
			}
		}
		if (implant.update) {
			await implant.update(child, implant, () => spawn(implant, element, child));
		}
		if (implant.children || implant.child) {
			await abduct(child, implant.children || [implant.child]);
		}
		if (implant.complete) {
			await implant.complete(child, implant, () => spawn(implant, element, child));
		}
	};

	let child = element.firstElementChild;
	for (let implant of implants) {
		await spawn(implant, element, child);
		child = child && child.nextElementSibling;
	}
	while (child) {
		let next = child && child.nextElementSibling;
		element.removeChild(child);
		child = next;
	}

}
