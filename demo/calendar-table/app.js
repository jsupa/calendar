const rows = document.getElementsByTagName("tr");

for (x = 0; x < rows.length; x++) {
    const items = rows[x].getElementsByTagName("td");
    for (y = 0; y < items.length; y++) {
        const beforItem = items[y - 1];
        const nextItem = items[y + 1];
        const item = items[y];
        if (beforItem == null && nextItem.className != item.className) {
            item.style.borderRadius = "100px";
        } else if (beforItem == null && nextItem.className == item.className) {
            item.style.borderRadius = "100px 0 0 100px";
        } else if (nextItem == null && beforItem.className != item.className) {
            item.style.borderRadius = "100px";
        } else if (nextItem == null && beforItem.className == item.className) {
            item.style.borderRadius = "0 100px 100px 0";
        } else if (
            beforItem.className == item.className &&
            nextItem.className == item.className
        ) {
            // ? nič, chcem mať rovný
        } else if (
            beforItem.className == item.className &&
            nextItem.className != item.className
        ) {
            item.style.borderRadius = "0 100px 100px 0";
        } else if (
            beforItem.className != item.className &&
            nextItem.className == item.className
        ) {
            item.style.borderRadius = "100px 0 0 100px";
        } else if (
            beforItem.className != item.className &&
            nextItem.className != item.className
        ) {
            item.style.borderRadius = "100px";
        }
    }
}

let arrayElements = document.getElementsByTagName("td");
for (let element of arrayElements) {
    element.addEventListener("mousemove", function (e) {
        var x = e.clientX;
        var y = e.clientY;
        element.style.cursor = "help";
        document.getElementById("tooltip").style.opacity = 1;
        document.getElementById("tooltip").innerText = element.className;
        document.getElementById("tooltip").style.left =
            x - document.getElementById("tooltip").offsetWidth / 2 + "px";
        document.getElementById("tooltip").style.top = y + 2 + "px";
    });
    element.addEventListener("mouseout", function (e) {
        document.getElementById("tooltip").style.opacity = 0;
    });
}
