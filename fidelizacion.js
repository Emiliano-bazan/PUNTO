function updatePointsGraph() {
    const graphFill = document.getElementById('pointsGraphFill');
    const circumference = 2 * Math.PI * 90; // 2πr
    const fillPercentage = (userPoints / maxPoints);
    const dashArray = circumference * fillPercentage;
    graphFill.style.strokeDasharray = `${dashArray} ${circumference}`;
    graphFill.style.strokeDashoffset = 0;
}

