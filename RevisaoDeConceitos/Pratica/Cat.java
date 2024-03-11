package Pratica;
import javax.swing.JOptionPane;



class Cat extends Animal{
        
    Cat(String name, double height, int weight) {
        super(name, height, weight);
    }

    public void scratch(){
            JOptionPane.showMessageDialog(null, "Gato arranhou");
    }

}