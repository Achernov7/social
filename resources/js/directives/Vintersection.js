export default {
    
    name: 'intersection',

    mounted(el, binding) {
        
        const options = {
            rootMargin: '0px',
            threshold: 0.5
        }

        const callback = (entries, observer) => {  
            if(entries[0].isIntersecting){
                if (typeof binding.value == 'function') {
                    binding.value()
                } else if (typeof binding.value == 'object') {
                    binding.value.function(binding.value.object)
                }
            }
        }

        const observer = new IntersectionObserver(callback, options)
        setTimeout(() => {
            observer.observe(el)
        }, 400);
    }
}