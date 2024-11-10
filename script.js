document.addEventListener('DOMContentLoaded', function() {
    const animalList = document.getElementById('animal-list');
    const animalImage = document.getElementById('animal-image');
    const toggleAnimals = document.getElementById('toggle-animals');
    const clearButton = document.getElementById('clear-button'); // Referensi tombol Clear

    // Toggle animal list visibility
    toggleAnimals.addEventListener('click', function() {
        if (animalList.style.maxHeight) {
            animalList.style.maxHeight = null;
        } else {
            animalList.style.maxHeight = animalList.scrollHeight + 'px';
        }
    });

    // Event listener for animal buttons
    animalList.addEventListener('click', function(event) {
        if (event.target.tagName === 'BUTTON') {
            const animal = event.target.getAttribute('data-animal');
            updateImage(animal);
        }
    });

    // Event listener for Clear button
    clearButton.addEventListener('click', function() {
        clearImage();
    });

    // Function to update animal image
    function updateImage(animal) {
        const imageUrl = `images/${animal}.jpg`; // URL gambar hewan
        animalImage.src = imageUrl;
        animalImage.style.display = 'block'; // Tampilkan gambar
        clearButton.style.display = 'block'; // Tampilkan tombol Clear
    }

    // Function to clear animal image
    function clearImage() {
        animalImage.src = ''; // Hapus sumber gambar
        animalImage.style.display = 'none'; // Sembunyikan gambar
        clearButton.style.display = 'none'; // Sembunyikan tombol Clear
    }

    // Check on page load if image is not present
    if (animalImage.src === '') {
        clearButton.style.display = 'none'; // Sembunyikan tombol Clear jika tidak ada gambar
    }
});
