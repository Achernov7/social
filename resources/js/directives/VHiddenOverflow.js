export default {
    
    name: 'hiddenOverflow',

    mounted(el, binding, vnode) {
        el.addEventListener("mouseenter", () => {
            el.style.overflowY = 'auto'
        })
        el.addEventListener("mouseleave", () => {
            el.style.overflowY = 'hidden'
        })
    }
}