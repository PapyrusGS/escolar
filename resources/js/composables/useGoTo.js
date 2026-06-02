export function useGoTo() {
    const goTo = (path) => {
        window.location.href = path;
    };
    return { goTo };
}
