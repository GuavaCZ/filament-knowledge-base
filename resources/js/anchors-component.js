export default function anchorsComponent() {
    return {

        init: async function () {
            let anchors = document.querySelectorAll('.gu-kb-anchor');

            let settings = {
                root: null,
                rootMargin: '-15% 0px -65% 0px',
                threshold: 0.1,
            };

            let observer = new IntersectionObserver(this.callback, settings);

            anchors.forEach(anchor => observer.observe(anchor));
        },

        callback: function (entries, observer) {
            let classes = [
                'transition', 'duration-300', 'ease-out', 'text-primary-600', 'dark:text-primary-400', 'translate-x-1'
            ];

            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    let section = '#' + entry.target.id;
                    document.querySelectorAll('.fi-sidebar-item-button .fi-sidebar-item-label')
                        .forEach((el) => el.classList.remove(...classes));
                    let el = document.querySelector('.fi-sidebar-item-button[href=\'' + section + '\'] .fi-sidebar-item-label');
                    el.classList.add(...classes);
                }
            });
        }
    }

};
