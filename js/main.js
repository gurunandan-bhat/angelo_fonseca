const inauguration = document.getElementById("exampleModal");

inauguration.addEventListener('show.bs.modal', event => {
    console.log('Modal displayed');
});

inauguration.addEventListener('hide.bs.modal', event => {
    console.log('Modal removed');
});
