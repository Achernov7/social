export default {
    
    name: 'hiddenOverflowX',

    mounted(el, binding, vnode) {
        el.addEventListener("mouseenter", () => {
            el.style.overflowX = 'auto'
        })
        el.addEventListener("mouseleave", () => {
            el.style.overflowX = 'hidden'
        })
    }
}