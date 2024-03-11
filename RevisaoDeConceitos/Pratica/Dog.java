package Pratica;
import javax.swing.JOptionPane;


class Dog extends Animal{
        
    Dog(String name, double height, int weight) {
        super(name, height, weight);
    }

    public void digHole(){
            JOptionPane.showMessageDialog(null, "Cachorro cavou");
    }

}
