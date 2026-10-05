/**
 * Chart Utilities - Global functions untuk chart customization
 * File ini berisi helper functions yang bisa dipakai oleh semua chart components
 */

/**
 * Generate array warna distinct menggunakan HSL algorithm
 * Fungsi ini menghasilkan warna-warna yang visual distinct dengan mendistribusikan
 * hue secara merata di color wheel (360 derajat)
 * 
 * @param {number} count - Jumlah warna yang dibutuhkan
 * @param {object} options - Optional configuration
 * @param {number} options.saturation - Saturation value (0-100), default 70
 * @param {number} options.lightness - Lightness value (0-100), default 60
 * @param {number} options.opacity - Opacity untuk background (0-1), default 0.2
 * @returns {object} Object dengan backgroundColor dan borderColor arrays
 * 
 * @example
 * // Generate 5 warna dengan setting default
 * const colors = generateDistinctColors(5);
 * console.log(colors.backgroundColor); // Array 5 warna HSLA dengan opacity 0.2
 * console.log(colors.borderColor);    // Array 5 warna HSL dengan opacity 1.0
 * 
 * @example
 * // Generate 3 warna dengan custom saturation & opacity
 * const colors = generateDistinctColors(3, { saturation: 80, opacity: 0.5 });
 */
function generateDistinctColors(count, options = {}) {
    const saturation = options.saturation || 70;
    const lightness = options.lightness || 60;
    const opacity = options.opacity !== undefined ? options.opacity : 0.2;
    
    const backgroundColors = [];
    const borderColors = [];
    
    for (let i = 0; i < count; i++) {
        // Distribusi merata di color wheel (360 derajat dibagi count)
        const hue = (i * 360 / count) % 360;
        
        // Background dengan opacity (untuk area fill chart)
        backgroundColors.push(`hsla(${hue}, ${saturation}%, ${lightness}%, ${opacity})`);
        
        // Border full opacity (untuk garis tepi chart)
        borderColors.push(`hsl(${hue}, ${saturation}%, ${lightness}%)`);
    }
    
    return {
        backgroundColor: backgroundColors,
        borderColor: borderColors
    };
}
