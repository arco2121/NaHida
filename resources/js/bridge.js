export const fetchData = () => {
    try {
        const data = document.querySelector("meta[name='params']");
        const temp = JSON.parse(data.getAttribute("content") || "{}");
        data.remove();
        return temp;
    } catch(e) {
        console.error(e);
    }
};
export const fetchEnv = () => {
    try {
        const data = document.querySelector("meta[name='env']");
        const temp = {
            ...import.meta.env,
            ...JSON.parse(data.getAttribute("content") || "{}")
        };
        data.remove();
        return temp;
    } catch(e) {
        console.error(e);
    }
};

export let fromServer = fetchData();
export let env = fetchEnv();

fromServer = Object.freeze(fromServer);
env = Object.freeze(env);
