import oauth from './oauth';
import sys from './sys';

const options = {
    oauth,
    sys,
};

export default function install(Vue) {
    if (install.installed) {
        console.warn('Vue components is already installed.');
        return;
    }
    install.installed = true;

    for (let component in options) {
        const componentInstaller = options[component];

        if (componentInstaller && component !== 'install') {
            Vue.use(componentInstaller);
        }
    }
}
