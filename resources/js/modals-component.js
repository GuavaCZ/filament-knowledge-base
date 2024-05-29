export default function modalsComponent() {
    return {

        init: async function () {
            this.hashChanged();
            window.addEventListener('hashchange', ()=>this.hashChanged());
        },

        hashChanged: function() {
            const fragment = location.hash.substring(1);
            const prefix = 'modal-';

            if (fragment.startsWith(prefix)) {
                const modal = fragment.substring(fragment.indexOf(prefix) + prefix.length);

                console.log('Open modal via wire: ', modal);
                this.$wire.showDocumentation(modal);
                // window.dispatchEvent(new CustomEvent('open-modal', {detail: {id: modal}}));
                history.replaceState(null, null, ' ');
            }
        },

    }

};
