package Pratica;
import javax.swing.JOptionPane;



class Bird extends Animal{
        
    Bird(String name, double height, int weight) {
        super(name, height, weight);
    }

    public void fly(){
            JOptionPane.showMessageDialog(null, "Pássaro voou");
    }

}