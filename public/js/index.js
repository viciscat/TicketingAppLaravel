let startMenuVisible = false;
const sideNav = document.getElementById('sidenav');

function setSideNavVisible(visible) {
    if (visible) sideNav.style.display = '';
    else sideNav.style.display = 'none';
}

function onResize() {
    if (window.innerWidth >= 768) setSideNavVisible(true);
    else setSideNavVisible(startMenuVisible);
}

function startMenuClick() {
    startMenuVisible = !startMenuVisible;
    setSideNavVisible(startMenuVisible);
}

onResize();
window.onresize = onResize