document.addEventListener('DOMcontentLoaded', function (){
    const searchInput = document.getElementById('searchInput');
    const courseItems = document.querySelectorAll('.course-item');

    searchInput.addEventListener('input', function ( event){
        const query = event.target.value.toLowerCase();

        courseItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(query) ? '' : 'none';
        })
    });
});